<?php

namespace App\Services;

use App\Helpers\BotHelper;
use App\Helpers\RequestHelper;
use App\Helpers\SessionHelper;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StatisticService
{
    public static function SendStatistic(string $page): ?PromiseInterface
    {
        $design = config('app.design');
        $design = str_replace('design_', '', $design);
        if (session()->has('uniq')) {
            $isUnique = 0;
        } else {
            $isUnique = 1;
            session(['uniq' => 1]);
        }

        $serverIp  = $_SERVER['SERVER_ADDR'] ?? '';
        $userIp    = RequestHelper::GetUserIp();
        $userAgent = request()->userAgent();

        if ($serverIp != $userIp
            && !in_array($userIp, ['80.79.4.17', '104.234.204.106', '20.105.137.134'])
            && !empty($userAgent)
            && !BotHelper::IsUserAgentBot()
        ) {
            $data = array(
                "sessid"      => SessionHelper::getSessionId(request()),
                "dt"          => date("Y-m-d H:i:s"),
                "ip"          => $userIp,
                "is_uniq"     => $isUnique,
                "country"     => session('location.country'),
                "aff"         => session('aff', 0),
                "saff"        => session('saff', ''),
                "domain_from" => request()->getHost(),
                "ref"         => session('referer', ''),
                "keyword"     => session('keyword', ''),
                "user_agent"  => $userAgent,
                "store_skin"  => $design,
                "theme"       => "",
                "duration"    => 0,
                "checkout"    => "",
                "route"       => "",
                "page"        => $page
            );

            $client = Http::async()->timeout(3);

            $queryString = http_build_query($data);
            $promise     = $client->get("http://true-serv.net/statistics/statistics.php?" . $queryString)->then();

            return $promise;
        }
        return null;
    }

    public static function SendCheckout()
    {
        $products  = [];
        $sessionId = '';

        foreach (session('cart') as $product) {
            $products[$product['pack_id']] = [
                'qty'            => $product['q'],
                'price'          => $product['price'],
                'is_ed_category' => false
            ];

            $sessionId = $product['cart_id'] ?? SessionHelper::getSessionId(request());
        }

        $products_str = base64_encode(serialize($products));
        $api_key      = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $data = [
            'method'      => 'checkout',
            'api_key'     => $api_key->key_data,
            'product'     => $products_str,
            'ip'          => RequestHelper::GetUserIp(),
            'sessid'      => $sessionId,
            'bonus'       => session('cart_option.bonus_id', 0),
            'country'     => session('location.country', 'US'),
            'total'       => session('total.checkout_total', 0),
            'customer_id' => session('form.customer_id', ''),
            'domain_from' => request()->getHost(),
            'fingerprint' => '',
            'keyword'     => session('keyword', ''),
            'email'       => session('form.email', ''),
            'aff'         => session('aff', 0),
            'saff'        => session('saff', ''),
            'ref'         => session('referer', ''),
            "user_agent"  => request()->userAgent(),
            'is_checkout_data_send' => session('is_checkout_data_send', 0)
        ];
        if (checkdnsrr('true-serv.net', 'A')) {
            try {
                $response = Http::timeout(3)->post('http://true-serv.net/checkout/order.php', $data);

                if ($response->successful()) {
                    // Обработка успешного ответа
                    $responseData = $response->json(); // Если ожидается JSON-ответ
                    session(['is_checkout_data_send' => 1]);
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
    }
}

