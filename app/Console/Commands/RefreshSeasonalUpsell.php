<?php

namespace App\Console\Commands;

use App\Services\SeasonalUpsellService;
use Illuminate\Console\Command;

class RefreshSeasonalUpsell extends Command
{
    protected $signature = 'seasonal-upsell:refresh {--force : Refresh before the seven-day TTL expires}';
    protected $description = 'Refresh the database snapshot of seasonal upsell products';

    public function handle(SeasonalUpsellService $service): int
    {
        try {
            $service->refresh((bool) $this->option('force'));
        } catch (\Throwable $e) {
            report($e);
            $this->error('Refresh failed; the last successful cache was preserved. Check the application log.');
            return self::FAILURE;
        }
        $this->info('Seasonal upsell cache is up to date.');
        return self::SUCCESS;
    }
}
