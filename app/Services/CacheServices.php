<?php


namespace App\Services;

use App\Models\CountryInfoCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CacheServices
{
    public static function InsertCountryInfo()
    {
        $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $data = [
            'method' => 'get_countries',
            'api_key' => $api_key->key_data
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
        $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $data = [
            'method' => 'get_countries',
            'api_key' => $api_key->key_data
        ];

        if(checkdnsrr('true-services.net', 'A'))
        {
            try {
                $response = Http::timeout(3)->post('http://true-services.net/checkout/order.php', $data);

                if ($response->successful()) {
                    // Обработка успешного ответа

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
                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (\Illuminate\Http\Client\RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
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