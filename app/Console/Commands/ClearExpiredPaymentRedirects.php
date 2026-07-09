<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ClearExpiredPaymentRedirects extends Command
{
    protected $signature = 'payment-redirects:clear-expired';

    protected $description = 'Delete expired payment redirect cache files from the file store';

    public function handle(): int
    {
        $store = Cache::store('payment_redirects');
        $driver = $store->getStore();

        $reflection = new \ReflectionClass($driver);
        $directory = $reflection->getProperty('directory')->getValue($driver);

        if (! is_dir($directory)) {
            $this->info('Cache directory does not exist.');

            return self::SUCCESS;
        }

        $deleted = 0;
        $directories = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $contents = file_get_contents($item->getRealPath());

                if ($contents === false || strlen($contents) < 10) {
                    continue;
                }

                $expiresAt = (int) substr($contents, 0, 10);

                if ($expiresAt < time()) {
                    unlink($item->getRealPath());
                    $deleted++;
                }
            } elseif ($item->isDir()) {
                $directories[] = $item->getRealPath();
            }
        }

        // Clean up empty directories (leaf directories first, thanks to CHILD_FIRST)
        foreach ($directories as $dirPath) {
            if ($dirPath === $directory) {
                continue;
            }

            if (is_dir($dirPath) && count(scandir($dirPath)) === 2) {
                rmdir($dirPath);
            }
        }

        Log::info('[ClearExpiredPaymentRedirects] cleaned {count} expired entries', ['count' => $deleted]);
        $this->info("Deleted {$deleted} expired payment redirect cache entries.");

        return self::SUCCESS;
    }
}
