<?php

namespace App\Services;

use App\Models\CountryInfoCache;
use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Log;

class GeoIpService
{
    private static function get_remote_file_size($url)
    {
        $sch = parse_url($url, PHP_URL_SCHEME);
        if (($sch != "http") && ($sch != "https") && ($sch != "ftp") && ($sch != "ftps")) {
            return false;
        }
        if (($sch == "http") || ($sch == "https")) {
            $headers = get_headers($url, 1);
            if ((!array_key_exists("Content-Length", $headers))) {
                return false;
            }
            return $headers["Content-Length"];
        }
        if (($sch == "ftp") || ($sch == "ftps")) {
            $server = parse_url($url, PHP_URL_HOST);
            $port = parse_url($url, PHP_URL_PORT);
            $path = parse_url($url, PHP_URL_PATH);
            $user = parse_url($url, PHP_URL_USER);
            $pass = parse_url($url, PHP_URL_PASS);
            if ((!$server) || (!$path)) {
                return false;
            }
            if (!$port) {
                $port = 21;
            }
            if (!$user) {
                $user = "anonymous";
            }
            if (!$pass) {
                $pass = "phpos@";
            }
            switch ($sch) {
                case "ftp":
                    $ftpid = ftp_connect($server, $port);
                    break;
                case "ftps":
                    $ftpid = ftp_ssl_connect($server, $port);
                    break;
            }
            if (!$ftpid) {
                return false;
            }
            $login = ftp_login($ftpid, $user, $pass);
            if (!$login) {
                return false;
            }
            $ftpsize = ftp_size($ftpid, $path);
            ftp_close($ftpid);
            if ($ftpsize == -1) {
                return false;
            }
            return $ftpsize;
        }
    }

    private static function set_downloading_file_info($downloading_info_file_name, $total_size, $current_size, $was_alredy_created = false)
    {
        if (!file_exists($downloading_info_file_name)) {
            if ($was_alredy_created) {
                return false;
            }
            $fd = fopen($downloading_info_file_name, 'w');
            fclose($fd);
        }
        if (is_writable($downloading_info_file_name)) {
            $tempfd = fopen($downloading_info_file_name, 'w');
            fwrite($tempfd, $total_size . "\r\n" . $current_size);
            fclose($tempfd);
            return true;
        } else {
            return false;
        }
    }

    private static function download_file($hostname, $path_from, $path_to, $downloading_info_file_name)
    {
        //
        // Определяем размер файла
        //
        $total_size = self::get_remote_file_size("http://" . $hostname . $path_from);
        self::set_downloading_file_info($downloading_info_file_name, $total_size, 0);
        //
        // Закачиваем файл
        //
        $fd = fsockopen($hostname, 80, $errno, $errstr, 30);
        $headers = "GET $path_from HTTP/1.1\r\n";
        $headers .= "Host: $hostname\r\n";
        $headers .= "Connection: Close\r\n\r\n";
        fwrite($fd, $headers);
        $start_content = false;
        $fds = fopen($path_to, "w");
        $cur_file_size = 0;
        $iteration = 0;

        while (!feof($fd)) {
            if (!$start_content) {
                $readed_str = fgets($fd, 1024);
            } else {
                $readed_str = fread($fd, 1024);
            }
            $cur_file_size += strlen($readed_str);
            if ($iteration % 20 == 0 && $iteration) {
                $result = self::set_downloading_file_info($downloading_info_file_name, $total_size, $cur_file_size, true);
                if (!$result) {
                    return false;
                }
            }
            if (trim($readed_str) == "" && !$start_content) {
                $start_content = true;
                continue;
            }
            if ($start_content) {
                fwrite($fds, $readed_str);
            }
            $iteration++;
        }
        fclose($fds);
        fclose($fd);
        self::set_downloading_file_info($downloading_info_file_name, $total_size, $total_size);
        unlink($downloading_info_file_name);
    }

    public static function GetInfoByIp()
    {
        // $ip = '89.187.179.179';//request()->ip();
        $ip = request()->ip();
        // $reader = new Reader(public_path() . '/GeoIp/GeoLite2-City.mmdb');

        $pathToGeoFile = public_path() . '/GeoIp/GeoIP2-City.mmdb';

        if (!file_exists($pathToGeoFile)) {
            $dbdump_rar_path = '/promo/GeoIP2-City.mmdb';
            self::download_file('true-meds.net', $dbdump_rar_path, public_path() . '/GeoIp/GeoIP2-City.mmdb', 'temp.txt');
            chmod(public_path() . '/GeoIp/GeoIP2-City.mmdb', 0777);
            $reader = new Reader(public_path() . '/GeoIp/GeoIP2-City.mmdb');
        } else {
            $reader = new Reader($pathToGeoFile);
        }

        try
        {
            $location = $reader->city($ip);

            $country_info = CountryInfoCache::query()
            ->where('country_iso2', '=', $location->country->isoCode)
            ->get();

            if($country_info->count() == 0)
            {
                $ip = '89.187.179.179';
                $location = $reader->city($ip);
            }

            $info = [
                'country' => $location->country->isoCode,
                'country_name' => strtolower($location->country->names['en']),
                'state' => $location->mostSpecificSubdivision->isoCode,
                'city' => $location->city->name,
                'postal' => $location->postal->code,
            ];
        }
        catch(\Exception $e)
        {
            Log::error($e->getMessage());
            $info = [
                'country' => 'US',
                'country_name' => strtolower('United states'),
                'state' => '',
                'city' => '',
                'postal' => '',
            ];
        }

        // $info = [
        //     'country' => 'US',
        //     'country_name' => strtolower('United states'),
        //     'state' => '',
        //     'city' => '',
        //     'postal' => '',
        // ];

        return $info;
    }

}

