<?php

namespace App\Services;

use App\Models\CountryInfoCache;
use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Log;

class GeoIpService
{
    public static function GetInfoByIp()
    {
        // $ip = '89.187.179.179';//request()->ip();
        $ip = request()->ip();
        $reader = new Reader(public_path() . '/GeoIp/GeoLite2-City.mmdb');

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

