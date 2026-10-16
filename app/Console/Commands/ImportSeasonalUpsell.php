<?php

namespace App\Console\Commands;

use App\Services\SeasonalUpsellService;
use Illuminate\Console\Command;

class ImportSeasonalUpsell extends Command
{
    protected $signature = 'seasonal-upsell:import {file : JSON file with a products array}';
    protected $description = 'Retired v1 import command; use the central seasonal API';

    public function handle(SeasonalUpsellService $service): int
    {
        $this->error('Local import is retired. Configure order.php get_seasonal_upsell and run seasonal-upsell:refresh --force.');
        return self::FAILURE;
    }
}
