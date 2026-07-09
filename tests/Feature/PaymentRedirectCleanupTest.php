<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class PaymentRedirectCleanupTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir = storage_path('framework/cache/payment-redirects-test-' . time());
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $this->removeDirectory($this->tempDir);
        }

        parent::tearDown();
    }

    public function test_cleans_expired_files_and_empty_directories(): void
    {
        // Create structure:
        // payment-redirects-test/
        // ├── expired_dir/
        // │   └── expired_file       (timestamp in past)
        // ├── mixed_dir/
        // │   ├── expired_file       (timestamp in past)
        // │   └── valid_file         (timestamp in future)
        // ├── empty_dir/             (should be removed)
        // ├── valid_dir/
        // │   └── valid_file         (timestamp in future)
        // └── expired_file_root      (timestamp in past)

        $past = time() - 3600;
        $future = time() + 3600;

        $this->makeCacheFile('expired_dir/expired_file', $past);
        $this->makeCacheFile('mixed_dir/expired_file', $past);
        $this->makeCacheFile('mixed_dir/valid_file', $future);
        mkdir($this->tempDir . '/empty_dir');
        $this->makeCacheFile('valid_dir/valid_file', $future);
        $this->makeCacheFile('expired_file_root', $past);

        // Run the cleanup logic (same as in ClearExpiredPaymentRedirects)
        $this->runCleanup($this->tempDir);

        // Assert expired files are deleted
        $this->assertFileDoesNotExist($this->tempDir . '/expired_dir/expired_file');
        $this->assertFileDoesNotExist($this->tempDir . '/mixed_dir/expired_file');
        $this->assertFileDoesNotExist($this->tempDir . '/expired_file_root');

        // Assert empty directories are deleted
        $this->assertDirectoryDoesNotExist($this->tempDir . '/expired_dir');
        $this->assertDirectoryDoesNotExist($this->tempDir . '/empty_dir');

        // Assert valid files and their parent directories are preserved
        $this->assertFileExists($this->tempDir . '/mixed_dir/valid_file');
        $this->assertFileExists($this->tempDir . '/valid_dir/valid_file');

        // Assert root directory itself still exists
        $this->assertDirectoryExists($this->tempDir);
    }

    public function test_skips_non_expired_files(): void
    {
        $future = time() + 3600;

        $this->makeCacheFile('keep_dir/keep_file', $future);

        $this->runCleanup($this->tempDir);

        $this->assertFileExists($this->tempDir . '/keep_dir/keep_file');
    }

    public function test_cleans_only_expired_files_with_50_limit(): void
    {
        $past = time() - 3600;
        $future = time() + 3600;

        // Create 60 expired files spread across directories
        for ($i = 0; $i < 60; $i++) {
            $dir = 'batch_dir_' . ($i % 10);
            $this->makeCacheFile("{$dir}/expired_{$i}", $past);
        }

        // Create a valid file
        $this->makeCacheFile('valid_dir/valid_file', $future);

        // Run cleanup with limit of 50
        $this->runCleanup($this->tempDir, 50);

        // Count remaining expired files
        $remaining = 0;
        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        ) as $file) {
            if ($file->isFile()) {
                $contents = file_get_contents($file->getRealPath());
                $expiresAt = (int) substr($contents, 0, 10);
                if ($expiresAt < time()) {
                    $remaining++;
                }
            }
        }

        // 10 expired files should remain (60 - 50)
        $this->assertEquals(10, $remaining, 'Should leave 10 expired files after 50-limit cleanup');

        // Valid file should still exist
        $this->assertFileExists($this->tempDir . '/valid_dir/valid_file');
    }

    private function makeCacheFile(string $relativePath, int $expiresAt): void
    {
        $fullPath = $this->tempDir . '/' . $relativePath;
        $dir = dirname($fullPath);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Laravel file cache format: {expiry:10d}{serialized data}
        $value = 'a:1:{s:3:"foo";s:3:"bar";}';
        file_put_contents($fullPath, str_pad((string) $expiresAt, 10, '0', STR_PAD_LEFT) . $value);
    }

    private function runCleanup(string $directory, int $limit = 0): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        $cleaned = 0;
        $directories = [];

        foreach ($iterator as $item) {
            if ($limit > 0 && $cleaned >= $limit) {
                break;
            }

            if ($item->isFile()) {
                $contents = file_get_contents($item->getRealPath());

                if ($contents === false || strlen($contents) < 10) {
                    continue;
                }

                $expiresAt = (int) substr($contents, 0, 10);

                if ($expiresAt < time()) {
                    unlink($item->getRealPath());
                    $cleaned++;
                }
            } elseif ($item->isDir()) {
                $directories[] = $item->getRealPath();
            }
        }

        foreach ($directories as $dirPath) {
            if ($dirPath === $directory) {
                continue;
            }

            if (is_dir($dirPath) && count(scandir($dirPath)) === 2) {
                rmdir($dirPath);
            }
        }
    }

    private function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }

        rmdir($dir);
    }
}
