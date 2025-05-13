<?php

namespace App\Services;

use App\Models\CountryInfoCache;
use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Log;

class GeoIpService
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

    public static function download_file($url, $path_to, $downloading_info_file_name)
    {
        $total_size = self::get_remote_file_size($url);

        if ($total_size === false) {
            Log::error("Failed to determine the size of the remote file: $url");
            return false;
        }

        if (!self::set_downloading_file_info($downloading_info_file_name, $total_size, 0)) {
            Log::error("Failed to initialize downloading info file: $downloading_info_file_name");
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $download_size, $downloaded, $upload_size, $uploaded) use ($downloading_info_file_name) {
            if ($download_size > 0) {
                self::set_downloading_file_info($downloading_info_file_name, $download_size, $downloaded, true);
            }
        });

        $file_content = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('cURL error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        if (file_put_contents($path_to, $file_content) === false) {
            Log::error("Failed to save the downloaded file to: $path_to");
            return false;
        }

        self::set_downloading_file_info($downloading_info_file_name, $total_size, $total_size);
        unlink($downloading_info_file_name);

        return true;
    }

    public static function GetInfoByIp()
    {
        // $ip = '89.187.179.179';//request()->ip();
        $ip = request()->headers->get('cf-connecting-ip') ?? request()->ip();

        $pathToGeoFileOutside = '/var/www/GeoIP2-City.mmdb';
        $pathToGeoFileInside = public_path() . '/GeoIp/GeoIP2-City.mmdb';

        if (file_exists($pathToGeoFileOutside)) {
            $reader = new Reader($pathToGeoFileOutside);
        } elseif (file_exists($pathToGeoFileInside)) {
            $reader = new Reader($pathToGeoFileInside);
        } else {
            $lockFile = storage_path('app/geoip.lock');

            if (!file_exists($lockFile)) {
                file_put_contents($lockFile, 'locked');
                try {
                    self::download_file('http://true-meds.net/promo/GeoIP2-City.mmdb', $pathToGeoFileInside, 'temp.txt');
                    chmod($pathToGeoFileInside, 0777);
                } catch (\Exception $e) {
                    Log::error('Failed to download GeoIP database: ' . $e->getMessage());
                    unlink($lockFile);
                    throw $e;
                }
                unlink($lockFile);
            } else {
                // sleep(5);
            }

            if (!file_exists($pathToGeoFileInside)) {
                throw new \Exception('GeoIP database file is missing after download attempt.');
            }

            $reader = new Reader($pathToGeoFileInside);
        }

        try
        {
            $location = $reader->city($ip);

            $country_info = CountryInfoCache::query()
                ->where('country_iso2', '=', $location->country->isoCode ?? null)
                ->get();

            if($country_info->isEmpty())
            {
                $ip = '89.187.179.179';
                $location = $reader->city($ip);
            }

            $info = [
                'country' => $location->country->isoCode ?? 'US',
                'country_name' => strtolower($location->country->names['en']) ?? 'united states',
                'state' => $location->mostSpecificSubdivision->isoCode ?? '',
                'city' => $location->city->name ?? '',
                'postal' => $location->postal->code ?? '',
            ];
        }
        catch(\Exception $e)
        {
            Log::error($e->getMessage());
            $info = [
                'country' => 'US',
                'country_name' => 'united states',
                'state' => '',
                'city' => '',
                'postal' => '',
            ];
        }

        return $info;
    }
}

