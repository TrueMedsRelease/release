<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\SeasonalUpsellService;

class SeasonalUpsellStatus extends Command
{
    protected $signature = 'seasonal-upsell:status';
    protected $description = 'Diagnose seasonal cache/configuration without calling the remote API';

    public function handle(SeasonalUpsellService $service): int
    {
        $this->line('Enabled: '.(config('seasonal_upsell.enabled') ? 'yes' : 'no'));
        foreach (['seasonal_upsell_cache' => ['id', 'items', 'refreshed_at']] as $table => $required) {
            if (!Schema::hasTable($table)) {
                $this->error('Missing table: '.$table);
                return self::FAILURE;
            }
            $missing = array_diff($required, Schema::getColumnListing($table));
            if ($missing) {
                $this->error($table.' missing columns: '.implode(', ', $missing));
                return self::FAILURE;
            }
        }
        $this->line('API key configured: '.(DB::table('shop_keys')->where('name_key', 'api_key')->value('key_data') ? 'yes' : 'no'));
        $cache = DB::table('seasonal_upsell_cache')->where('id', 1)->first();
        $this->line('Last successful refresh: '.($cache->refreshed_at ?? 'never'));
        $snapshot = $cache ? json_decode($cache->items, true, 512, JSON_THROW_ON_ERROR) : [];
        $format = $snapshot['format'] ?? null;
        if (!in_array($format, [2, 3], true)) {
            $this->warn('Country feed not initialized. Run seasonal-upsell:refresh --force.');
            return self::FAILURE;
        }
        $this->line('Snapshot format: '.$format);
        $countryRows = [];
        foreach ($snapshot['countries'] as $code => $products) {
            if ($format === 3) {
                $details = [];
                foreach ($products as $id => $settings) {
                    $details[] = $id.' (autocheck '.$settings['autocheck'].', packs '.count($settings['prices']).')';
                }
                $countryRows[] = [$code, implode(', ', $details), count($products)];
            } else {
                $countryRows[] = [$code, implode(', ', $products), count($products)];
            }
        }
        $this->table(['Country', 'Product IDs in display order', 'Count'], $countryRows);
        $items = $snapshot['products'];
        $this->line('IDs in cache: '.count($items));
        $rows = [];
        foreach ($items as $item) {
            $id = $item['product_id'];
            $product = DB::table('product')->where('id', $id)->first();
            $packs = DB::table('product_packaging')->where('product_id', $id)->where('is_showed', 1)
                ->where('price', '>', 0)->where('num', '>', 0)->count();
            $rows[] = [$id, $product ? 'yes' : 'no', !empty($product->is_showed) ? 'yes' : 'no',
                $packs, $service->eligible((int) $id) ? 'allowed' : 'excluded'];
        }
        $this->table(['Product ID', 'Exists', 'Visible', 'Available packs', 'Local filter'], $rows);
        $this->line('Browser /api/seasonal-upsell additionally applies session locale, country and cart exclusions.');
        return self::SUCCESS;
    }
}
