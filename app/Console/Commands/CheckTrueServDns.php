<?php

namespace App\Console\Commands;

use App\Services\TrueServService;
use Illuminate\Console\Command;

class CheckTrueServDns extends Command
{
    protected $signature = 'trueserv:check-dns';

    public function handle(): int
    {
        $this->info('dns: ' . (TrueServService::refresh() ? 'OK' : 'DOWN'));
        return self::SUCCESS;
    }
}