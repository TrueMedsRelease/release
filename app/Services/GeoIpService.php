<?php

namespace App\Services;

use App\Models\CountryInfoCache;
use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Log;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Support\Facades\App;

class GeoIpService
{
    // private static function get_remote_file_size($url)
    // {
    //     $parsed_url = parse_url($url);
    //     $scheme = $parsed_url['scheme'] ?? null;

    //     if (!in_array($scheme, ['http', 'https', 'ftp', 'ftps'])) {
    //         return false;
    //     }

    //     if (in_array($scheme, ['http', 'https'])) {
    //         $headers = get_headers($url, 1);
    //         if (!isset($headers['Content-Length'])) {
    //             return false;
    //         }
    //         return (int)$headers['Content-Length'];
    //     }

    //     if (in_array($scheme, ['ftp', 'ftps'])) {
    //         $server = $parsed_url['host'] ?? null;
    //         $port = $parsed_url['port'] ?? 21;
    //         $path = $parsed_url['path'] ?? null;
    //         $user = $parsed_url['user'] ?? 'anonymous';
    //         $pass = $parsed_url['pass'] ?? 'phpos@';

    //         if (!$server || !$path) {
    //             return false;
    //         }

    //         $ftpid = ($scheme === 'ftp') ? ftp_connect($server, $port) : ftp_ssl_connect($server, $port);
    //         if (!$ftpid) {
    //             return false;
    //         }

    //         $login = ftp_login($ftpid, $user, $pass);
    //         if (!$login) {
    //             ftp_close($ftpid);
    //             return false;
    //         }

    //         $size = ftp_size($ftpid, $path);
    //         ftp_close($ftpid);

    //         return ($size >= 0) ? $size : false;
    //     }

    //     return false;
    // }

    // private static function set_downloading_file_info($downloading_info_file_name, $total_size, $current_size, $was_already_created = false)
    // {
    //     if (!file_exists($downloading_info_file_name)) {
    //         if ($was_already_created) {
    //             return false;
    //         }
    //         touch($downloading_info_file_name);
    //     }

    //     if (!is_writable($downloading_info_file_name)) {
    //         return false;
    //     }

    //     file_put_contents($downloading_info_file_name, "$total_size\r\n$current_size");
    //     return true;
    // }

    // public static function download_file($url, $path_to, $downloading_info_file_name)
    // {
    //     $total_size = self::get_remote_file_size($url);

    //     if ($total_size === false) {
    //         Log::error("Failed to determine the size of the remote file: $url");
    //         return false;
    //     }

    //     if (!self::set_downloading_file_info($downloading_info_file_name, $total_size, 0)) {
    //         Log::error("Failed to initialize downloading info file: $downloading_info_file_name");
    //         return false;
    //     }

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    //     curl_setopt($ch, CURLOPT_NOPROGRESS, false);
    //     curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $download_size, $downloaded, $upload_size, $uploaded) use ($downloading_info_file_name) {
    //         if ($download_size > 0) {
    //             self::set_downloading_file_info($downloading_info_file_name, $download_size, $downloaded, true);
    //         }
    //     });

    //     $file_content = curl_exec($ch);
    //     if (curl_errno($ch)) {
    //         Log::error('cURL error: ' . curl_error($ch));
    //         curl_close($ch);
    //         return false;
    //     }

    //     curl_close($ch);

    //     if (file_put_contents($path_to, $file_content) === false) {
    //         Log::error("Failed to save the downloaded file to: $path_to");
    //         return false;
    //     }

    //     self::set_downloading_file_info($downloading_info_file_name, $total_size, $total_size);
    //     unlink($downloading_info_file_name);

    //     return true;
    // }

    public static function downloadGeoIpDatabase(string $url, string $destinationPath): bool
    {
        $directory = dirname($destinationPath);

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0775, true) && !is_dir($directory)) {
                Log::error("Cannot create GeoIP directory: {$directory}");
                return false;
            }
        }

        if (!is_writable($directory)) {
            Log::error("GeoIP directory is not writable: {$directory}");
            return false;
        }

        $temporaryPath = $destinationPath . '.download';

        $fileHandle = fopen($temporaryPath, 'wb');

        if ($fileHandle === false) {
            Log::error("Cannot create temporary GeoIP file: {$temporaryPath}");
            return false;
        }

        $curl = curl_init();

        if ($curl === false) {
            fclose($fileHandle);
            @unlink($temporaryPath);

            Log::error('Cannot initialize cURL for GeoIP download.');
            return false;
        }

        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_FILE           => $fileHandle,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT        => 180,
            CURLOPT_USERAGENT      => 'GeoIpDatabaseDownloader/1.0',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HEADER         => false,
        ]);

        try {
            $result = curl_exec($curl);
            $curlError = curl_error($curl);
            $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $contentType = (string) curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        } finally {
            curl_close($curl);
            fclose($fileHandle);
        }

        if ($result === false) {
            @unlink($temporaryPath);

            Log::error("GeoIP download failed: {$curlError}");

            return false;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            @unlink($temporaryPath);

            Log::error("GeoIP download returned HTTP {$httpCode}. URL: {$url}");

            return false;
        }

        clearstatcache(true, $temporaryPath);

        $downloadedSize = @filesize($temporaryPath);

        if ($downloadedSize === false || $downloadedSize === 0) {
            @unlink($temporaryPath);

            Log::error('Downloaded GeoIP database is empty.');
            return false;
        }

        if (
            stripos($contentType, 'text/html') !== false
            || stripos($contentType, 'application/json') !== false
        ) {
            @unlink($temporaryPath);

            Log::error("Unexpected GeoIP response content type: {$contentType}");

            return false;
        }

        try {
            $testReader = new Reader($temporaryPath);
            $testReader->close();
        } catch (\Throwable $e) {
            @unlink($temporaryPath);

            Log::error('Downloaded file is not a valid GeoIP database: ' . $e->getMessage());

            return false;
        }

        if (!@rename($temporaryPath, $destinationPath)) {
            if (
                !@copy($temporaryPath, $destinationPath)
                || !@unlink($temporaryPath)
            ) {
                @unlink($temporaryPath);

                Log::error("Cannot move GeoIP database to: {$destinationPath}");

                return false;
            }
        }

        @chmod($destinationPath, 0644);

        clearstatcache(true, $destinationPath);

        Log::info("GeoIP database downloaded successfully. " . "Path: {$destinationPath}; size: {$downloadedSize} bytes.");

        return true;
    }

    public static function GetInfoByIp()
    {
        // $ip = '5.187.2.250';//request()->ip();
        $ip = request()->headers->get('cf-connecting-ip') ?? request()->ip();

        $ip = trim((string) $ip);

        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip, 2)[0]);
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            Log::warning("Invalid IP address for GeoIP lookup: {$ip}");

            return [
                'country'      => 'US',
                'country_name' => 'united states',
                'state'        => '',
                'city'         => '',
                'postal'       => '',
            ];
        }

        $pathToGeoFileOutside = config('geoip.outside_path');
        $pathToGeoFileInside = public_path('GeoIp/GeoIP2-City.mmdb');
        $pathToGeoFileInsideDir = dirname($pathToGeoFileInside);

        $reader = null;

        if ($pathToGeoFileOutside) {
            $reader = self::openGeoIpReader($pathToGeoFileOutside);
        }

        if (!$reader) {
            $reader = self::openGeoIpReader($pathToGeoFileInside);
        }

        // if (!$reader) {
        //     try {
        //         if (!@is_dir($pathToGeoFileInsideDir)) {
        //             if (
        //                 !@mkdir($pathToGeoFileInsideDir, 0775, true)
        //                 && !@is_dir($pathToGeoFileInsideDir)
        //             ) {
        //                 Log::warning("Cannot create GeoIP directory: {$pathToGeoFileInsideDir}");
        //             }
        //         }

        //         if (
        //             @is_dir($pathToGeoFileInsideDir)
        //             && @is_writable($pathToGeoFileInsideDir)
        //         ) {
        //             $lockPath = $pathToGeoFileInsideDir . '/geoip.lock';
        //             $lock = @fopen($lockPath, 'c');

        //             if ($lock === false) {
        //                 Log::warning("Cannot open GeoIP lock file: {$lockPath}");
        //             } else {
        //                 $locked = false;

        //                 try {
        //                     $locked = flock($lock, LOCK_EX);

        //                     if (!$locked) {
        //                         Log::warning("Cannot acquire GeoIP lock: {$lockPath}");
        //                     } else {

        //                         if (!@is_readable($pathToGeoFileInside)) {
        //                             $downloadUrl = (string) config('geoip.download_url', 'https://true-meds.net/promo/GeoIP2-City.mmdb');

        //                             if ($downloadUrl === '') {
        //                                 Log::error('GeoIP download URL is empty.');
        //                                 $downloaded = false;
        //                             } else {
        //                                 $downloaded = self::downloadGeoIpDatabase($downloadUrl, $pathToGeoFileInside);
        //                             }

        //                             if (!$downloaded) {
        //                                 Log::warning('GeoIP database could not be downloaded.');
        //                             }

        //                             clearstatcache(true, $pathToGeoFileInside);

        //                             if (!$reader && @is_readable($pathToGeoFileInside)) {
        //                                 try {
        //                                     $reader = new Reader($pathToGeoFileInside);
        //                                 } catch (\Throwable $e) {
        //                                     Log::warning("GeoIP reader failed after download: {$e->getMessage()}");
        //                                 }
        //                             }
        //                         }
        //                     }
        //                 } catch (\Throwable $e) {
        //                     Log::error('GeoIP download routine failed: ' . $e->getMessage());
        //                 } finally {
        //                     if ($locked) {
        //                         @flock($lock, LOCK_UN);
        //                     }

        //                     @fclose($lock);
        //                 }
        //             }
        //         } else {
        //             Log::warning("Public GeoIP directory is not writable: " . $pathToGeoFileInsideDir);
        //         }
        //     } catch (\Throwable $e) {
        //         Log::error('GeoIP directory preparation failed: ' . $e->getMessage());
        //     }

        //     $reader = self::openGeoIpReader($pathToGeoFileInside);
        // }

        if (!$reader) {
            Log::warning('GeoIP database is unavailable; returning default location.');

            return [
                'country'      => 'US',
                'country_name' => 'united states',
                'state'        => '',
                'city'         => '',
                'postal'       => '',
            ];
        }

        try {
            $location = $reader->city($ip);
            $iso2 = $location->country->isoCode ?? null;

            if (!$iso2) {
                throw new \RuntimeException(
                    'Country ISO code was not found in GeoIP response.'
                );
            }

            $countryExists = CountryInfoCache::query()
                ->where('country_iso2', $iso2)
                ->exists();

            if (!$countryExists) {
                return [
                    'country'      => 'US',
                    'country_name' => 'united states',
                    'state'        => '',
                    'city'         => '',
                    'postal'       => '',
                ];
            }

            return [
                'country' => $iso2,
                'country_name' => strtolower($location->country->names['en'] ?? 'united states'),
                'state' => $location->mostSpecificSubdivision->isoCode ?? '',
                'city' => $location->city->name ?? '',
                'postal' => $location->postal->code ?? '',
            ];
        } catch (\Throwable $e) {
            Log::error('GeoIP lookup failed: ' . $e->getMessage());

            return [
                'country'      => 'US',
                'country_name' => 'united states',
                'state'        => '',
                'city'         => '',
                'postal'       => '',
            ];
        }
    }

    private static function openGeoIpReader(?string $path): ?Reader
    {
        if (!$path) {
            return null;
        }

        try {
            if (!@is_file($path) || !@is_readable($path)) {
                return null;
            }

            return new Reader($path);
        } catch (\Throwable $e) {
            Log::warning("GeoIP database cannot be opened at {$path}: {$e->getMessage()}");

            return null;
        }
    }
}

