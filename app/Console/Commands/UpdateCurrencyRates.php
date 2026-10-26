<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateCurrencyRates extends Command
{
    public const VERSION_KEY = 'currency_rates_version';

    protected $signature = 'currency:update-rates';
    protected $description = 'Update currency.coef from true-serv rates API';

    public function handle(): int
    {
        try {
            $rates = Http::timeout(15)->retry(3, 2000)
                ->get(config('services.currency_rates.url'))
                ->throw()
                ->json();
        } catch (\Throwable $e) {
            report($e);
            $this->error('Rates request failed; currency table was not changed.');
            return self::FAILURE;
        }

        if (!is_array($rates) || empty($rates)) {
            $this->error('Rates API returned an empty or invalid response.');
            return self::FAILURE;
        }

        $updated = 0;
        foreach ($rates as $rate) {
            $code = strtolower($rate['currency_code'] ?? '');
            $coef = (float) ($rate['exchange_rate'] ?? 0);

            if ($code === '' || $coef <= 0) {
                continue;
            }

            $updated += DB::table('currency')->where('code', '=', $code)->update(['coef' => $coef]);
        }

        Cache::forever(self::VERSION_KEY, now()->timestamp);

        $this->info("Updated $updated currencies.");
        return self::SUCCESS;
    }
}
