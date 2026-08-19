<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Product;
use App\Models\ProductSearch;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CatalogCursorService
{
    public const PER_PAGE = 12;

    private const CACHE_TTL = 180;

    public function paginate(
        string $design,
        string $search,
        string $category,
        ?string $cursor = null,
        int $perPage = self::PER_PAGE
    ): array {
        if ($search !== '') {
            return $this->paginateSearch($search, $design, $cursor, $perPage);
        }

        $rows = $this->pageRows($design, $category, $cursor, $perPage);

        $hasMore = count($rows) > $perPage;
        $pageRows = array_slice($rows, 0, $perPage);
        $pageIds = array_map(fn($row) => $row['id'], $pageRows);
        $nextCursor = $hasMore ? $this->encodeCursor($pageRows[count($pageRows) - 1]) : null;

        return [
            'products'    => $this->hydrate($pageIds),
            'total'       => $this->total($design, $category),
            'has_more'    => $hasMore,
            'next_cursor' => $nextCursor,
        ];
    }

    /**
     * Keyset (cursor) pagination on product.main_order ASC with id tiebreak.
     */
    private function pageRows(string $design, string $category, ?string $cursor, int $perPage): array
    {
        $query = $this->baseQuery($design, $category);

        if ($cursor !== null && $cursor !== '') {
            [$cursorOrder, $cursorId] = $this->decodeCursor($cursor);

            $query->where(function ($q) use ($cursorOrder, $cursorId) {
                $q->where('product.main_order', '>', $cursorOrder)
                    ->orWhere(function ($q2) use ($cursorOrder, $cursorId) {
                        $q2->where('product.main_order', '=', $cursorOrder)
                            ->where('product.id', '>', $cursorId);
                    });
            });
        }

        return $query
            ->orderBy('product.main_order')
            ->orderBy('product.id')
            ->limit($perPage + 1)
            ->get()
            ->map(fn($row) => ['id' => (int) $row->id, 'main_order' => (int) $row->main_order])
            ->all();
    }

    private function encodeCursor(array $row): string
    {
        return $row['main_order'] . ':' . $row['id'];
    }

    private function decodeCursor(string $cursor): array
    {
        $parts = explode(':', $cursor);

        return [(int) ($parts[0] ?? 0), (int) ($parts[1] ?? 0)];
    }

    /**
     * Search results are ordered by relevance (exact matches first), not by id,
     * so cursor here is the zero-based position in the ordered id list.
     */
    private function paginateSearch(string $search, string $design, ?string $cursor, int $perPage): array
    {
        $ids = $this->searchIds($search, $design);

        $total = count($ids);

        $position = $cursor === null ? 0 : (int) $cursor;

        $pageIds = array_slice($ids, $position, $perPage);
        $nextPosition = $position + count($pageIds);
        $hasMore = $nextPosition < $total;

        return [
            'products'    => $this->hydrate($pageIds),
            'total'       => $total,
            'has_more'    => $hasMore,
            'next_cursor' => $hasMore ? $nextPosition : null,
        ];
    }

    private function searchIds(string $search, string $design): array
    {
        $search = trim($search);
        if ($search === '') {
            return [];
        }

        $country = strtoupper(session('location.country') ?? '');

        $key = implode('|', [
            'catalog_search_ids',
            $design,
            $search,
            $country,
            env('APP_GIFT_CARD'),
        ]);

        return Cache::remember($key, 60, function () use ($search, $country) {
            if (str_contains($search, ' ')) {
                $searchFullText = '"' . $search . '"';
            } else {
                $searchFullText = $search . '*';
            }

            $searchTextLower      = strtolower(urldecode($search));
            $searchFullTextLower = strtolower(urldecode($searchFullText));

            $exact = ProductSearch::whereRaw('LOWER(keyword) = ?', [$searchTextLower])
                ->where('is_showed', '=', 1);

            $partial = ProductSearch::whereFullText('keyword', $searchFullTextLower, ['mode' => 'boolean'])
                ->where('is_showed', '=', 1);

            if ((int) env('APP_GIFT_CARD') === 0) {
                $exact->where('product_id', '<>', 616);
                $partial->where('product_id', '<>', 616);
            }

            $exactIds = $exact->distinct()->pluck('product_id')->map(fn($id) => (int) $id)->all();

            $partial->whereNotIn('product_id', $exactIds);
            $partialIds = $partial->distinct()->pluck('product_id')->map(fn($id) => (int) $id)->all();

            $ids = array_merge($exactIds, $partialIds);

            $ids = array_values(array_unique($ids));

            if ((int) env('APP_GIFT_CARD') === 0) {
                $ids = array_values(array_filter($ids, fn($id) => $id !== 616));
            }

            if (!in_array($country, ['US', 'GB', 'AU'], true)) {
                $ids = array_values(array_filter($ids, fn($id) => !in_array($id, [755, 491], true)));
            }
            if ($country !== 'US') {
                $ids = array_values(array_filter($ids, fn($id) => $id !== 1204));
            }

            return $ids;
        });
    }

    private function baseQuery(string $design, string $category)
    {
        $query = Product::query()
            ->select('product.id', 'product.main_order')
            ->distinct()
            ->where('product.is_showed', '=', 1)
            ->join('product_category', 'product.id', '=', 'product_category.product_id')
            ->join('category', 'category.id', '=', 'product_category.category_id')
            ->where('category.is_showed', '=', 1)
            ->whereNull('category.category_parent_id');

        if ($category !== '') {
            $query->where('category.url', '=', $category);
        }

        if ((int) env('APP_GIFT_CARD') === 0) {
            $query->where('product.id', '<>', 616);
        }

        $country = strtoupper(session('location.country') ?? '');
        if (!in_array($country, ['US', 'GB', 'AU'], true)) {
            $query->whereNotIn('product.id', [755, 491]);
        }
        if ($country !== 'US') {
            $query->whereNotIn('product.id', [1204]);
        }

        $languageId = $this->languageId();
        $query->whereExists(function ($q) use ($languageId) {
            $q->selectRaw('1')
                ->from('product_desc')
                ->whereColumn('product_desc.product_id', '=', 'product.id')
                ->where('product_desc.language_id', '=', $languageId);
        });

        $query->whereExists(function ($q) {
            $q->selectRaw('1')
                ->from('product_packaging')
                ->whereColumn('product_packaging.product_id', '=', 'product.id')
                ->where('product_packaging.is_showed', '=', 1)
                ->where('product_packaging.price', '<>', 0);
        });

        return $query;
    }

    private function total(string $design, string $category): int
    {
        $country = strtoupper(session('location.country') ?? '');
        $domain  = preg_replace('/\.[^.]+$/', '', request()->getHost());

        $key = implode('|', [
            'catalog_total',
            $design,
            $category,
            App::currentLocale(),
            $country,
            session('aff', env('APP_AFF')),
            env('APP_GIFT_CARD'),
        ]);

        return (int) Cache::remember($key, self::CACHE_TTL, function () use ($design, $category) {
            return $this->baseQuery($design, $category)->count('product.id');
        });
    }

    /**
     * Hydrate only the requested page of products (matches ProductServices output shape).
     */
    private function hydrate(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $productsDesc = Cache::remember(App::currentLocale() . '_products_desc', self::CACHE_TTL, function () {
            return ProductServices::GetProductDesc($this->languageId());
        });

        $productPillPrices = Cache::remember(App::currentLocale() . '_product_pill_prices', self::CACHE_TTL, function () {
            return ProductServices::GetAllProductPillPrice();
        });

        $dosagesData = $this->dosagesData();

        $countryCode = strtoupper(session('location.country') ?? '');
        $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
        $isAffSpecial = $this->isAffIdInSpecialUrlList();

        $rows = Product::query()
            ->whereIn('id', $ids)
            ->orderByRaw('FIELD(id, ' . implode(',', array_map('intval', $ids)) . ')')
            ->get(['id', 'image', 'aktiv'])
            ->toArray();

        $products = [];
        foreach ($rows as &$product) {
            if (!isset($productsDesc[$product['id']])) {
                continue;
            }

            $product['name'] = $productsDesc[$product['id']]['name'];
            $product['desc'] = $productsDesc[$product['id']]['desc'];

            if ($isAffSpecial) {
                if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                    $product['url'] = 'Buying_' . $productsDesc[$product['id']]['url'] . '_online';
                } else {
                    $product['url'] = __('text.text_aff_domain_1')
                        . '_' . $productsDesc[$product['id']]['url']
                        . '_' . __('text.text_aff_domain_2');
                }
            } else {
                $product['url'] = $productsDesc[$product['id']]['url'];
            }

            $product['aktiv'] = explode(',', ucwords(trim(str_replace("\r\n", '', trim($product['aktiv'])))));
            $product['alt']   = $product['image'];

            if ($product['id'] != 616) {
                if ($isAffSpecial) {
                    $product['image'] = $domainWithoutZone . '_' . $product['image'];
                    $product['alt']   = __('text.text_aff_domain_1')
                        . '_' . $product['name']
                        . '_' . __('text.text_aff_domain_2');
                }
            }

            foreach ($product['aktiv'] as $key => $value) {
                $activeUrl = str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))));

                if ($isAffSpecial) {
                    if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                        $activeUrl = 'Buying_' . $activeUrl . '_online';
                    } else {
                        $activeUrl = __('text.text_aff_domain_1')
                            . '_' . $activeUrl
                            . '_' . __('text.text_aff_domain_2');
                    }
                }

                $product['aktiv'][$key] = [
                    'name' => trim($value),
                    'url'  => $activeUrl,
                ];

                $product['product_dosages'] = $dosagesData[$product['id']] ?? [];
            }

            $product['price']    = $productPillPrices[$product['id']]['price'] ?? 0;
            $product['discount'] = $productPillPrices[$product['id']]['discount'] ?? 0;

            $products[] = $product;
        }

        return $products;
    }

    private function dosagesData(): array
    {
        return Cache::remember('catalog_dosages', self::CACHE_TTL, function () {
            $rows = DB::table('product_dosage as pd')
                ->join('product_packaging as pp', function ($join) {
                    $join->on('pp.product_id', '=', 'pd.product_id')
                        ->on('pd.id', '=', 'pp.product_dosage_id')
                        ->where('pp.is_showed', 1);
                })
                ->select('pd.product_id', 'pd.dosage')
                ->distinct()
                ->get()
                ->toArray();

            $data = [];

            foreach ($rows as $row) {
                if (!isset($row->product_id, $row->dosage)) {
                    continue;
                }

                $id  = $row->product_id;
                $dos = trim((string) $row->dosage);

                if (!array_key_exists($id, $data)) {
                    $data[$id] = [];
                }
                if ($dos !== '' && !in_array($dos, $data[$id], true)) {
                    $data[$id][] = $dos;
                }
            }

            foreach ($data as &$dosages) {
                usort($dosages, fn($a, $b) =>
                    (float) preg_replace('/[^\d.,]/', '', str_replace(',', '.', $a))
                    <=>
                    (float) preg_replace('/[^\d.,]/', '', str_replace(',', '.', $b))
                );
            }

            return $data;
        });
    }

    private function languageId(): int
    {
        return isset(Language::$languages[App::currentLocale()])
            ? Language::$languages[App::currentLocale()]
            : Language::$languages['en'];
    }

    private function isAffIdInSpecialUrlList(): bool
    {
        $affId = session('aff') ?? env('APP_AFF');

        return in_array($affId, [1799, 1947, 1952, 1957], true);
    }
}
