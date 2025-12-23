<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RenewalService
{
    private static function get_remote_file_size($url)
    {
        $parsed_url = parse_url($url);
        $scheme = $parsed_url['scheme'] ?? null;

        if (!in_array($scheme, ['http', 'https', 'ftp', 'ftps'])) {
            return false;
        }

        if (in_array($scheme, ['http', 'https'])) {
            $headers = get_headers($url, 1);
            if (!isset($headers['Content-Length'])) {
                return false;
            }
            return (int)$headers['Content-Length'];
        }

        if (in_array($scheme, ['ftp', 'ftps'])) {
            $server = $parsed_url['host'] ?? null;
            $port = $parsed_url['port'] ?? 21;
            $path = $parsed_url['path'] ?? null;
            $user = $parsed_url['user'] ?? 'anonymous';
            $pass = $parsed_url['pass'] ?? 'phpos@';

            if (!$server || !$path) {
                return false;
            }

            $ftpid = ($scheme === 'ftp') ? ftp_connect($server, $port) : ftp_ssl_connect($server, $port);
            if (!$ftpid) {
                return false;
            }

            $login = ftp_login($ftpid, $user, $pass);
            if (!$login) {
                ftp_close($ftpid);
                return false;
            }

            $size = ftp_size($ftpid, $path);
            ftp_close($ftpid);

            return ($size >= 0) ? $size : false;
        }

        return false;
    }

    private static function set_downloading_file_info($downloading_info_file_name, $total_size, $current_size, $was_already_created = false)
    {
        if (!file_exists($downloading_info_file_name)) {
            if ($was_already_created) {
                return false;
            }
            touch($downloading_info_file_name);
        }

        if (!is_writable($downloading_info_file_name)) {
            return false;
        }

        file_put_contents($downloading_info_file_name, "$total_size\r\n$current_size");
        return true;
    }

    public static function download_file(string $url, string $path_to, string $downloading_info_file_name): bool
    {
        $total_size = self::get_remote_file_size($url);

        if ($total_size === false) {
            Log::warning("Failed to determine the size of the remote file (will download anyway): $url");
            $total_size = 0;
        }

        if (!self::set_downloading_file_info($downloading_info_file_name, $total_size, 0)) {
            Log::error("Failed to initialize downloading info file: $downloading_info_file_name");
            return false;
        }

        $fp = @fopen($path_to, 'wb');
        if ($fp === false) {
            Log::error("Failed to open local file for writing: $path_to");
            @unlink($downloading_info_file_name);
            return false;
        }

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_FILE           => $fp,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FAILONERROR    => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_NOPROGRESS     => false,
            CURLOPT_PROGRESSFUNCTION => function (
                $resource,
                float $download_size,
                float $downloaded,
                float $upload_size,
                float $uploaded
            ) use ($downloading_info_file_name, $total_size) {
                // download_size из cURL может быть 0/−1, тогда используем то, что было из HEAD
                $total = $download_size > 0 ? (int)$download_size : (int)$total_size;
                if ($total <= 0) {
                    return;
                }

                self::set_downloading_file_info(
                    $downloading_info_file_name,
                    $total,
                    (int)$downloaded,
                    true
                );
            },
            CURLOPT_USERAGENT      => 'DomainInstaller/1.0',
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            $err = curl_error($ch);
            $code = curl_errno($ch);
            Log::error("cURL error ($code): $err while downloading $url");
            curl_close($ch);
            fclose($fp);
            @unlink($path_to);
            @unlink($downloading_info_file_name);
            return false;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        if ($httpCode < 200 || $httpCode >= 300) {
            Log::error("HTTP error $httpCode while downloading $url");
            @unlink($path_to);
            @unlink($downloading_info_file_name);
            return false;
        }

        $final_size = @filesize($path_to);
        if ($final_size === false || $final_size === 0) {
            Log::error("Downloaded file is empty or unreadable: $path_to");
            @unlink($path_to);
            @unlink($downloading_info_file_name);
            return false;
        }

        if ($total_size === 0) {
            $total_size = $final_size;
        }
        self::set_downloading_file_info($downloading_info_file_name, (int)$total_size, (int)$final_size);

        @unlink($downloading_info_file_name);

        return true;
    }

    public static function renewalDatabase()
    {
        $pathToRenewalData = storage_path('app/public/update_dump.sql');

        $downloadOk = self::download_file(
            'https://true-meds.net/promo/update_dump_full.sql',
            $pathToRenewalData,
            storage_path('app/public/update_dump.info')
        );

        if (!$downloadOk) {
            Log::error("renewalDatabase: download_file() вернул false");

            return response()->json([
                'status' => 'ERROR',
                'text'   => "Failed to download dump file (см. storage/logs/laravel.log)",
            ], 500);
        }

        if (!file_exists($pathToRenewalData)) {
            return response()->json([
                'status' => 'ERROR',
                'text'   => "Dump file not found: $pathToRenewalData",
            ], 500);
        }

        if (file_exists($pathToRenewalData)) {
            // $db_user = env('DB_USERNAME');
            // $db_password = env('DB_PASSWORD');
            // $db_name = env('DB_DATABASE');
            // $aff = env('APP_AFF');
            // $url = env('APP_URL');

            // exec("mysql -u $db_user -p $db_password $db_name < $pathToRenewalData");

            try {
                $db_user     = env('DB_USERNAME');
                $db_password = env('DB_PASSWORD');
                $db_name     = env('DB_DATABASE');
                $aff         = env('APP_AFF');
                $url         = env('APP_URL');

                $command = sprintf(
                    'mysql -u%s -p%s %s < %s',
                    escapeshellarg($db_user),
                    escapeshellarg($db_password),
                    escapeshellarg($db_name),
                    escapeshellarg($pathToRenewalData)
                );

                $output   = [];
                $exitCode = 0;
                exec($command . ' 2>&1', $output, $exitCode);

                if ($exitCode !== 0) {
                    Log::error('renewalDatabase: mysql import failed', [
                        'exit_code' => $exitCode,
                        'output'    => $output,
                    ]);

                    return response()->json([
                        'status' => 'ERROR',
                        'text'   => 'Importing the dump into the database failed with an error.',
                        'debug'  => $output,
                    ], 500);
                }

                if ($aff == 1054) {
                    DB::table('category')
                        ->where('id', '!=', 14)
                        ->where('id', '!=', 13)
                        ->update([
                            'is_showed' => 0
                        ]);

                    DB::table('product')
                        ->whereIn('id', function($q) {
                            $q->select('product_category.product_id')
                                ->distinct()
                                ->from('product_category')
                                ->join('category', 'category.id', '=', 'product_category.category_id')
                                ->where('category.is_showed', 0);
                        })
                        ->update([
                            'is_showed'          => 0,
                            'is_showed_on_main'  => 0,
                            'is_showed_in_menu'  => 0,
                        ]);

                    DB::table('product_search')
                        ->whereIn('product_id', function($q) {
                            $q->select('product.id')
                                ->from('product')
                                ->where('product.is_showed', '=', 0);
                        })
                        ->update([
                            'is_showed' => 0,
                        ]);
                }

                $url = rtrim($url, '/');

                if ($url == "trueph24.com" || $url == "trueplls.com") {
                    $need_price = DB::table('product_packaging')
                        ->where('id', '=', 10096)
                        ->get(['price'])
                        ->toArray();

                    if ($need_price) {
                        $currentPrice = (int)$need_price['price'];

                        if ($currentPrice < 250) {
                            DB::table('product_packaging')
                                ->whereIn('product_id', [511, 300])
                                ->update([
                                    'price' => DB::raw('ROUND(price * 0.9, 2)'),
                                    'min_price' => DB::raw('ROUND(min_price * 0.9, 2)')
                                ]);
                        }
                    }
                }

                if ($url == 'globalpharmaexpress.com') {

                    $updates = [
                        10223 => ['price' => 20,  'min_price' => 16],
                        10224 => ['price' => 24,  'min_price' => 19],
                        10225 => ['price' => 27,  'min_price' => 22],
                        10226 => ['price' => 53,  'min_price' => 43],
                        10227 => ['price' => 76,  'min_price' => 61],
                        10228 => ['price' => 101, 'min_price' => 81],
                        10229 => ['price' => 152, 'min_price' => 122],
                        10230 => ['price' => 228, 'min_price' => 182],
                        10231 => ['price' => 304, 'min_price' => 243],

                        10232 => ['price' => 12,  'min_price' => 10],
                        10233 => ['price' => 15,  'min_price' => 12],
                        10234 => ['price' => 18,  'min_price' => 14],
                        10235 => ['price' => 34,  'min_price' => 27],
                        10236 => ['price' => 49,  'min_price' => 39],
                        10237 => ['price' => 65,  'min_price' => 52],
                        10238 => ['price' => 98,  'min_price' => 78],
                        10239 => ['price' => 147, 'min_price' => 118],
                        10240 => ['price' => 196, 'min_price' => 157],

                        2616  => ['price' => 20,  'min_price' => 16],
                        2617  => ['price' => 38,  'min_price' => 30],
                        2618  => ['price' => 57,  'min_price' => 46],
                        2619  => ['price' => 76,  'min_price' => 61],
                        2620  => ['price' => 114, 'min_price' => 91],
                        2621  => ['price' => 172, 'min_price' => 138],
                        2622  => ['price' => 229, 'min_price' => 183],

                        2623  => ['price' => 18,  'min_price' => 14],
                        2624  => ['price' => 33,  'min_price' => 26],
                        2625  => ['price' => 48,  'min_price' => 38],
                        2626  => ['price' => 65,  'min_price' => 52],
                        2627  => ['price' => 97,  'min_price' => 78],
                        2628  => ['price' => 146, 'min_price' => 117],
                        2629  => ['price' => 195, 'min_price' => 156],

                        2564  => ['price' => 34,  'min_price' => 27],
                        2565  => ['price' => 66,  'min_price' => 53],
                        2566  => ['price' => 98,  'min_price' => 78],
                        2567  => ['price' => 132, 'min_price' => 106],
                        2568  => ['price' => 201, 'min_price' => 161],
                        2569  => ['price' => 310, 'min_price' => 248],

                        10418 => ['price' => 20,  'min_price' => 16],
                        10419 => ['price' => 26,  'min_price' => 21],
                        2372  => ['price' => 29,  'min_price' => 23],
                        2373  => ['price' => 58,  'min_price' => 46],
                        2374  => ['price' => 86,  'min_price' => 69],
                        2375  => ['price' => 114, 'min_price' => 91],
                        2376  => ['price' => 167, 'min_price' => 134],
                        2377  => ['price' => 243, 'min_price' => 194],
                        10420 => ['price' => 340, 'min_price' => 272],

                        10421 => ['price' => 18,  'min_price' => 14],
                        10422 => ['price' => 24,  'min_price' => 19],
                        2366  => ['price' => 30,  'min_price' => 24],
                        2367  => ['price' => 51,  'min_price' => 41],
                        2368  => ['price' => 73,  'min_price' => 58],
                        2369  => ['price' => 96,  'min_price' => 77],
                        2370  => ['price' => 145, 'min_price' => 116],
                        2371  => ['price' => 227, 'min_price' => 182],
                        10423 => ['price' => 320, 'min_price' => 256],

                        10424 => ['price' => 15,  'min_price' => 12],
                        10425 => ['price' => 20,  'min_price' => 16],
                        2396  => ['price' => 25,  'min_price' => 20],
                        2397  => ['price' => 41,  'min_price' => 33],
                        2398  => ['price' => 62,  'min_price' => 50],
                        2399  => ['price' => 82,  'min_price' => 66],
                        2400  => ['price' => 120, 'min_price' => 96],
                        2401  => ['price' => 171, 'min_price' => 137],
                        10426 => ['price' => 217, 'min_price' => 174],

                        10427 => ['price' => 14,  'min_price' => 11],
                        10428 => ['price' => 19,  'min_price' => 15],
                        2378  => ['price' => 24,  'min_price' => 19],
                        2379  => ['price' => 40,  'min_price' => 32],
                        2380  => ['price' => 60,  'min_price' => 48],
                        2381  => ['price' => 80,  'min_price' => 64],
                        2382  => ['price' => 110, 'min_price' => 88],
                        2383  => ['price' => 150, 'min_price' => 120],
                        10429 => ['price' => 201, 'min_price' => 161],

                        11938 => ['price' => 40,  'min_price' => 32],
                        11939 => ['price' => 78,  'min_price' => 62],
                        11940 => ['price' => 108, 'min_price' => 86],
                        14073 => ['price' => 142, 'min_price' => 114],
                        14074 => ['price' => 212, 'min_price' => 170],
                        14075 => ['price' => 317, 'min_price' => 254],
                        14076 => ['price' => 420, 'min_price' => 336],

                        10079 => ['price' => 28,  'min_price' => 22],
                        10080 => ['price' => 37,  'min_price' => 30],
                        10081 => ['price' => 53,  'min_price' => 42],
                        10082 => ['price' => 105, 'min_price' => 84],
                        10083 => ['price' => 157, 'min_price' => 126],
                        10084 => ['price' => 209, 'min_price' => 167],
                        10085 => ['price' => 312, 'min_price' => 250],
                        10086 => ['price' => 465, 'min_price' => 372],
                        10087 => ['price' => 618, 'min_price' => 494],

                        10406 => ['price' => 22,  'min_price' => 18],
                        10407 => ['price' => 28,  'min_price' => 22],
                        2688  => ['price' => 38,  'min_price' => 30],
                        2689  => ['price' => 68,  'min_price' => 54],
                        2690  => ['price' => 99,  'min_price' => 79],
                        2691  => ['price' => 129, 'min_price' => 103],
                        2692  => ['price' => 186, 'min_price' => 149],
                        2693  => ['price' => 279, 'min_price' => 223],
                        10408 => ['price' => 371, 'min_price' => 297],
                    ];

                    DB::transaction(function () use ($updates) {
                        foreach ($updates as $id => $vals) {
                            DB::table('product_packaging')
                                ->where('id', $id)
                                ->update($vals);
                        }
                    });
                }

                return response()->json([
                    'status' => 'OK',
                    'text'   => 'Database update completed successfully.',
                ]);
            } catch (\Throwable $e) {
                Log::error('renewalDatabase: exception', [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]);

                return response()->json([
                    'status' => 'ERROR',
                    'text'   => 'An error occurred while updating the database: ' . $e->getMessage(),
                ], 500);
            } finally {
                if (file_exists($pathToRenewalData)) {
                    @unlink($pathToRenewalData);
                }
            }
        } else {
            return json_encode([
                'status' => 'ERROR',
                'text' => "Failed to save the downloaded file to: $pathToRenewalData"
            ]);
        }
    }

    public static function renewalShop() {
        exec('git update-index --no-skip-worktree public/robots.txt public/sitemap.xml; git update-index --assume-unchanged public/robots.txt public/sitemap.xml; git stash; git pull;');

        $replUrl = rtrim(env('APP_URL'), '/');
        $replUrl = str_replace(['https://', 'http://'], '', $replUrl);
        $filesToPatch = array(public_path('robots.txt'), public_path('sitemap.xml'));

        foreach ($filesToPatch as $f) {
            if (file_exists($f)) {
                if (is_writable($f)) {
                    $c = file_get_contents($f);
                    $c = str_replace('#URL#', $replUrl, $c);
                    file_put_contents($f, $c);
                } else {
                    return json_encode([
                        'status' => 'ERROR',
                        'text' => "File {$f} is not writable - URL could not be supplied.\n"
                    ]);
                }
            } else {
                return json_encode([
                    'status' => 'ERROR',
                    'text' => "File {$f} not found - unable to substitute URL.\n"
                ]);
            }
        }

        exec('git update-index --no-skip-worktree public/robots.txt public/sitemap.xml; git update-index --no-assume-unchanged public/robots.txt public/sitemap.xml;');

        return response()->json([
            'status' => 'OK',
            'text'   => 'Shop update completed successfully.',
        ]);
    }
}