<?php

namespace App\Console\Commands;

use App\Services\GeoIpService;
use GeoIp2\Database\Reader;
use Illuminate\Console\Command;

class DownloadGeoIpDatabase extends Command
{
    protected $signature = 'geoip:download {--force : Скачать базу заново, даже если файл уже существует}';
    protected $description = 'Download GeoIP2 City database to public/GeoIp';

    public function handle(): int
    {
        $url = (string) config('geoip.download_url', 'https://true-meds.net/promo/GeoIP2-City.mmdb');

        $destinationPath = public_path('GeoIp/GeoIP2-City.mmdb');

        if ($url === '') {
            $this->error('GeoIP download URL is empty.');

            return self::FAILURE;
        }

        if (
            !$this->option('force')
            && is_readable($destinationPath)
        ) {
            try {
                $reader = new Reader($destinationPath);
                $reader->close();

                $this->info("GeoIP database already exists: {$destinationPath}");

                return self::SUCCESS;
            } catch (\Throwable $e) {
                $this->warn('Existing GeoIP database is invalid. ' . 'It will be downloaded again.');
            }
        }

        $this->info("Downloading GeoIP database from: {$url}");
        $this->info("Destination: {$destinationPath}");

        try {
            $downloaded = GeoIpService::downloadGeoIpDatabase($url, $destinationPath);
        } catch (\Throwable $e) {
            $this->error('GeoIP download failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (!$downloaded) {
            $this->error('GeoIP database could not be downloaded. ' . 'Check storage/logs/laravel.log.');
            return self::FAILURE;
        }

        $this->info('GeoIP database downloaded successfully.');

        return self::SUCCESS;
    }
}