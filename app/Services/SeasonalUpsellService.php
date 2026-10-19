<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\Language;
use App\Models\ProductPackaging;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;


class SeasonalUpsellService
{
    public string $reason = 'ready';

    public function configured(): bool
    {
        return (bool) config('seasonal_upsell.enabled');
    }

    public function eligible(int $productId): bool
    {
        if ($productId === 616) return false;
        $blocked = config('seasonal_upsell.excluded_categories', []);
        if (!$blocked) return true;
        return !DB::table('product_category')->where('product_id', $productId)
            ->whereIn('category_id', $blocked)->exists();
    }

    public static function normalizePrice($price): string
    {
        if ((!is_string($price) && !is_int($price) && !is_float($price))
            || !preg_match('/^(0|[1-9][0-9]{0,9})(?:\.[0-9]{1,2})?$/D', (string) $price)
            || (float) $price <= 0) {
            throw new \RuntimeException('Invalid seasonal pack price.');
        }
        $parts = explode('.', (string) $price, 2);
        return $parts[0].'.'.str_pad($parts[1] ?? '', 2, '0');
    }

    public static function normalizeFeed($object): array
    {
        if (!is_object($object) || count((array) $object) > 250) {
            throw new \RuntimeException('Seasonal API must return a country object.');
        }
        $countries = [];
        foreach ($object as $country => $products) {
            $code = strtoupper((string) $country);
            if (!preg_match('/^[A-Z]{2}$/D', $code) || array_key_exists($code, $countries)
                || !is_object($products) || count((array) $products) > 200) {
                throw new \RuntimeException('Invalid country or product settings.');
            }
            $countries[$code] = [];
            foreach ($products as $productKey => $settings) {
                $id = filter_var($productKey, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                if ($id === false || (string) $id !== (string) $productKey || !is_object($settings)
                    || !isset($settings->autocheck, $settings->packs)
                    || !is_int($settings->autocheck) || $settings->autocheck <= 0
                    || !is_array($settings->packs) || !$settings->packs || count($settings->packs) > 200) {
                    throw new \RuntimeException('Invalid seasonal product settings.');
                }
                $prices = [];
                foreach ($settings->packs as $pack) {
                    if (!is_object($pack) || !isset($pack->pack_id, $pack->price)
                        || !is_int($pack->pack_id) || $pack->pack_id <= 0
                        || array_key_exists($pack->pack_id, $prices)) {
                        throw new \RuntimeException('Invalid or duplicate seasonal pack.');
                    }
                    $prices[$pack->pack_id] = self::normalizePrice($pack->price);
                }
                if (!array_key_exists($settings->autocheck, $prices)) {
                    throw new \RuntimeException('Autocheck must belong to the allowed packs.');
                }
                $countries[$code][$id] = ['autocheck' => $settings->autocheck, 'prices' => $prices];
            }
        }
        return $countries;
    }

    public static function ensureTable(): void
    {
        if (Schema::hasTable('seasonal_upsell_cache')) return;
        Schema::create('seasonal_upsell_cache', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->json('items');
            $table->timestamp('refreshed_at')->nullable();
        });
    }

    public function refresh(bool $force = false): void
    {
        self::ensureTable();
        DB::table('seasonal_upsell_cache')->insertOrIgnore(['id' => 1, 'items' => '[]']);
        DB::transaction(function () use ($force) {
            $cache = DB::table('seasonal_upsell_cache')->where('id', 1)->lockForUpdate()->first();
            if (!$force && $cache->refreshed_at && Carbon::parse($cache->refreshed_at)->gt(now()->subDays(7))) return;
            $apiKey = DB::table('shop_keys')->where('name_key', 'api_key')->value('key_data');
            if (!$apiKey) throw new \RuntimeException('Missing shop_keys.api_key.');
            $client = Http::acceptJson()->connectTimeout(3)->timeout(10)->withoutRedirecting();
            if (config('seasonal_upsell.as_form')) $client = $client->asForm();
            $response = $client->post(config('seasonal_upsell.endpoint'), [
                'method' => 'get_seasonal_upsell', 'api_key' => $apiKey,
            ]);
            if (!$response->successful()) throw new \RuntimeException('Seasonal API returned HTTP '.$response->status());
            $object = json_decode($response->body(), false, 512, JSON_THROW_ON_ERROR);
            $countries = self::normalizeFeed($object);
            $ids = [];
            foreach ($countries as $products) {
                foreach ($products as $id => $settings) {
                    $ids[] = $id;
                    $owners = DB::table('product_packaging')->whereIn('id', array_keys($settings['prices']))
                        ->pluck('product_id', 'id');
                    foreach ($settings['prices'] as $packId => $price) {
                        if (!isset($owners[$packId]) || (int) $owners[$packId] !== $id) {
                            throw new \RuntimeException('Seasonal pack does not belong to product '.$id.': '.$packId);
                        }
                    }
                }
            }
            $ids = array_values(array_unique($ids));
            $items = $this->snapshotForIds($ids);
            DB::table('seasonal_upsell_cache')->where('id', 1)->update([
                'items' => json_encode(['format' => 3, 'countries' => (object) $countries, 'products' => $items], JSON_THROW_ON_ERROR), 'refreshed_at' => now(),
            ]);
        });
    }

    public function snapshotForIds(array $ids): array
    {
        $products = DB::table('product')->whereIn('id', $ids)->get(['id', 'image', 'is_showed'])->keyBy('id');
        $descriptions = DB::table('product_desc')->whereIn('product_id', $ids)
            ->get(['product_id', 'language_id', 'name', 'url'])->groupBy('product_id');
        $packs = DB::table('product_packaging')->whereIn('product_id', $ids)->where('is_showed', 1)
            ->where('price', '>', 0)->where('num', '>', 0)->orderBy('price')->orderBy('id')
            ->get(['id', 'product_id', 'dosage', 'num', 'type_id', 'price'])->groupBy('product_id');
        $items = [];
        foreach ($ids as $id) {
            $product = $products[$id] ?? null;
            $items[] = [
                'product_id' => $id,
                'exists' => $product !== null, 'is_showed' => (bool) ($product->is_showed ?? false),
                'image' => $product->image ?? null,
                'descriptions' => isset($descriptions[$id]) ? $descriptions[$id]->values()->all() : [],
                'packs' => isset($packs[$id]) ? $packs[$id]->values()->all() : [],
            ];
        }
        return $items;
    }

    public function offers(): array
    {
        $this->reason = 'ready';
        if (!$this->configured()) {
            $this->reason = 'disabled';
            return [];
        }

        $cache = DB::table('seasonal_upsell_cache')->where('id', 1)->first();
        if (!$cache || !$cache->refreshed_at) {
            $this->reason = 'not_initialized';
            return [];
        }
        $snapshot = json_decode($cache->items, true, 512, JSON_THROW_ON_ERROR);

        if (!in_array($snapshot['format'] ?? null, [2, 3], true)) {
            $this->reason = 'country_feed_required';
            return [];
        }
        $country = strtoupper((string) session('location.country', ''));
        if (!preg_match('/^[A-Z]{2}$/D', $country)) {
            $this->reason = 'country_unknown';
            return [];
        }

        $countrySettings = $snapshot['format'] === 3 ? ($snapshot['countries'][$country] ?? []) : [];
        $countryIds = $snapshot['format'] === 3 ? array_keys($countrySettings) : ($snapshot['countries'][$country] ?? []);
        if (!$countryIds) {
            $this->reason = 'no_country_offers';
            return [];
        }
        $byId = array_column($snapshot['products'], null, 'product_id');
        $cachedItems = [];
        foreach ($countryIds as $id) {
            if (isset($byId[$id])) $cachedItems[] = $byId[$id];
        }
        $cartIds = array_column(session('cart', []), 'product_id');
        $items = array_values(array_filter($cachedItems, function ($item) use ($cartIds) {
            return !in_array($item['product_id'], $cartIds)
                && $this->eligible($item['product_id']);
        }));
        if (!$items) {
            $this->reason = 'filtered_out';
            return [];
        }

        $data = ProductServices::getProductData(array_column($items, 'product_id'), session('design', config('app.design')));
        $products = [];
        foreach ($data as $product) {
            $products[(int) $product['id']] = $product;
        }
        $lang = Language::$languages[app()->getLocale()] ?? Language::$languages['en'];
        $types = DB::table('product_type_desc')->where('language_id', $lang)->pluck('name', 'type_id');
        $offers = [];
        foreach ($items as $item) {
            $product = $products[$item['product_id']] ?? null;
            if (!$product) {
                continue;
            }
            $packs = ProductPackaging::query()->where('product_id', $item['product_id'])
                ->where('is_showed', 1)->where('price', '>', 0)->where('num', '>', 0)
                ->orderBy('id')->get();

            $packs = $packs->groupBy('dosage')->sortKeysDesc(SORT_NUMERIC)
                ->flatMap(fn ($group) => $group->sortByDesc('num')->values())
                ->values();

            $maxUnitPrices = [];
            foreach ($packs as $pack) {
                $dosage = (string) $pack->dosage;
                $maxUnitPrices[$dosage] = max(
                    $maxUnitPrices[$dosage] ?? 0,
                    round((float) $pack->price / (int) $pack->num, 2)
                );
            }
            $options = [];
            $settings = $countrySettings[$item['product_id']] ?? null;
            foreach ($packs as $pack) {
                if ($settings !== null && !array_key_exists($pack->id, $settings['prices'])) continue;
                $usdPrice = self::normalizePrice($settings['prices'][$pack->id] ?? $pack->price);
                $comparePrice = $maxUnitPrices[(string) $pack->dosage] * (int) $pack->num;
                $currentPrice = round((float) $usdPrice * (float) session('currency_c', 1), 2);
                $convertedCompare = round($comparePrice * (float) session('currency_c', 1), 2);
                $hasSaving = $comparePrice > (float) $usdPrice && $convertedCompare > $currentPrice;
                $options[] = [
                    'pack_id' => (int) $pack->id,
                    'label' => $pack->dosage.' × '.$pack->num.' '.($types[$pack->type_id] ?? ''),
                    'price' => $currentPrice,
                    'price_usd' => $usdPrice,
                    'compare_price' => $hasSaving ? $convertedCompare : null,
                    'discount_percent' => $hasSaving
                        ? (int) ceil(100 - ((float) $usdPrice / $comparePrice) * 100)
                        : 0,
                ];
            }
            $autocheck = $settings['autocheck'] ?? ($options[0]['pack_id'] ?? null);
            if ($options && in_array($autocheck, array_column($options, 'pack_id'), true)) {
                $offers[] = [
                    'product_id' => $item['product_id'],
                    'autocheck' => $autocheck,
                    'name' => $product['name'], 'url' => route('home.product', $product['url']),
                    'image' => route('home.set_images', $product['image']), 'packs' => $options,
                ];
            }
            if (count($offers) >= config('seasonal_upsell.limit', 4)) {
                break;
            }
        }
        if (!$offers) $this->reason = 'catalog_unavailable';
        return $offers;
    }

    public function currency(): array
    {
        $code = session('currency', 'usd');
        return ['code' => strtoupper($code), 'symbol' => Currency::$prefix[$code] ?? '$'];
    }
}
