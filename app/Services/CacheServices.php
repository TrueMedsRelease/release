<?php


namespace App\Services;

use App\Models\CountryInfoCache;
use Illuminate\Support\Facades\Http;

class CacheServices
{
    public static function InsertCountryInfo()
    {
        $data = [
            'method' => 'get_countries',
            'api_key' => '7c73d5ca242607050422af5a4304ef71'
        ];

        $response = Http::post('http://true-services.net/checkout/order.php', $data);

        $response = json_decode($response, true);

        foreach($response['countries'] as $key => $country)
        {
            $country_cache = new CountryInfoCache();

            $country_cache->country_iso2 = $key;
            $country_cache->country_name = $country['name'];
            $country_cache->info = json_encode(['secret_package' => $country['secret_package'], 'ems' => $country['ems'], 'regular' => $country['regular']]);
            $country_cache->date = date('Y-m-d H:i:s');

            $country_cache->save();

            unset($country_cache);
        }

        return 'OK';
    }

    public static function UpdateCountryInfo()
    {
        $data = [
            'method' => 'get_countries',
            'api_key' => '7c73d5ca242607050422af5a4304ef71'
        ];

        $response = Http::post('http://true-services.net/checkout/order.php', $data);

        $response = json_decode($response, true);

        foreach($response['countries'] as $key => $country)
        {
            $country_cache = CountryInfoCache::where('country_iso2', $key)->first();

            if(!empty($country_cache['country_iso2']))
            {
                $country_cache['country_name'] = $country['name'];
                $country_cache['info'] = json_encode(['secret_package' => $country['secret_package'], 'ems' => $country['ems'], 'regular' => $country['regular']]);
                $country_cache['date'] = date('Y-m-d H:i:s');

                $country_cache->save();
            }
            else
            {
                $country_cache = new CountryInfoCache();

                $country_cache->country_iso2 = $key;
                $country_cache->country_name = $country['name'];
                $country_cache->info = json_encode(['secret_package' => $country['secret_package'], 'ems' => $country['ems'], 'regular' => $country['regular']]);
                $country_cache->date = date('Y-m-d H:i:s');

                $country_cache->save();

                unset($country_cache);
            }

            unset($country_cache);
        }

        return 'OK';
    }

    public static function CheckCountryInfo()
    {
        $info = CountryInfoCache::latest('date')->first();

        if(!empty($info['date']))
        {
            $now = time();
            $your_date = strtotime($info['date']);

            $datediff = $now - $your_date;
            $diff = round($datediff / (60 * 60 * 24));
            if($diff >= 7)
            {
                CacheServices::UpdateCountryInfo();
            }
        }
        else
        {
            CacheServices::InsertCountryInfo();
        }
    }
}