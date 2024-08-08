<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Log;

class GeoIpService
{
    public static function GetInfoByIp()
    {
        $ip = '89.187.179.179';//request()->ip();
        $reader = new Reader(public_path() . '\GeoIp\GeoLite2-City.mmdb');
        try
        {
            $location = $reader->city($ip);
            $info = [
                'country' => $location->country->isoCode,
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
                'state' => '',
                'city' => '',
                'postal' => '',
            ];
        }

        return $info;
    }
}

