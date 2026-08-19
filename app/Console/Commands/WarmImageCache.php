<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class WarmImageCache extends Command
{
    protected $signature = 'images:warm {--img=webp} {--url=} {--delay=100}';
    protected $description = 'Pre-download product images into public/images';

    public function handle(): int
    {
        $dir = public_path('images');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $ext = $this->option('img');

        $water = $this->option('url')
            ?: str_replace(['http://', 'https://'], '', rtrim(config('app.url'), '/'));

        $images = Product::query()
            ->where('is_showed', 1)
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->pluck('image')
            ->unique()
            ->values();

        $bar = $this->output->createProgressBar(count($images));
        $bar->start();

        $ok = 0;
        $skipped = 0;

        foreach ($images as $image) {
            $path = $dir . '/' . $image . '.' . $ext;

            // Если файл уже есть и он не битый — пропускаем
            if (is_file($path) && filesize($path) > 0 && @file_get_contents($path) !== 'error') {
                $bar->advance();
                continue;
            }

            $url = 'https://true-serv.net/support/images_for_shops/image_return_new.php?pill='
                 . urlencode($image) . '&img=' . $ext . '&url=' . $water;

            // Качаем через cURL с принудительным IPv4 и игнором SSL
            $data = $this->fetchWithCurl($url);

            if ($data !== false && $data !== '' && $data !== 'error') {
                @file_put_contents($path, $data);
                $ok++;
            } else {
                $skipped++;
                $this->line('');
                $this->warn('skip: ' . $image . ' | url=' . $water);
            }

            usleep((int) $this->option('delay') * 1000);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. ok={$ok}, skipped={$skipped}, files: " . count(glob($dir . '/*.' . $ext)));

        return self::SUCCESS;
    }

    private function fetchWithCurl(string $url)
    {
        if (!function_exists('curl_init')) {
            // Fallback, если расширение curl вдруг не установлено
            $ctx = stream_context_create([
                'http' => ['timeout' => 10],
                'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
            ]);
            return @file_get_contents($url, false, $ctx);
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false, // Игнорируем ошибки SSL-сертификатов
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4, // ПРИНУДИТЕЛЬНО IPv4 (лечит Handshake timed out)
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) ImageWarmer/1.0',
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}