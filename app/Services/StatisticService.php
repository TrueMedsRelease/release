<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StatisticService
{
    public static function SendStatistic(string $page)
    {
        $design = config('app.design');
        $design = str_replace('design_', '', $design);
        if(session()->has('uniq'))
        {
            $is_uniq = 0;
        }
        else
        {
            $is_uniq = 1;
            session(['uniq' => 1]);
        }

        $bots = [
            'googlebot', 'bingbot', 'yandex', 'slurp', 'duckduckbot', 'baiduspider',
            'sogou', 'exabot', 'facebot', 'ia_archiver', 'ahrefsbot', 'semrushbot',
            'mj12bot', 'dotbot', 'rogerbot', 'megaindex', 'blexbot', 'yoozbot', 'bot', 'spider', 'PetalBot'
        ];

        $userAgent = strtolower(request()->userAgent());
        $bot = false;

        foreach ($bots as $bo) {
            if (strpos($userAgent, $bo) !== false) {
                $bot = true;
                break;
            }
        }

        $server_ip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
        $user_ip = request()->ip();
        $user_agent = request()->userAgent();

        if ($server_ip != $user_ip && !in_array($user_ip, ['80.79.4.17', '104.234.204.106', '20.105.137.134']) && (isset($user_agent) && ($user_agent != '' || $user_agent != ' ' || !empty($user_agent)))) {
            if(!$bot)
            {
                $get = array(
                    "sessid" => session('_token'),
                    "dt" => date("Y-m-d H:i:s"),
                    "ip" => $user_ip,
                    "is_uniq" => $is_uniq,
                    "country" => session('location.country'),
                    "aff" => session('aff', 0),
                    "saff" => session('saff', ''),
                    "domain_from" => request()->getHost(),
                    "ref" => session('referer', ''),
                    "keyword" => session('keyword', ''),
                    "user_agent" => $user_agent,
                    "store_skin" => $design,
                    "theme" => "",
                    "duration" => 0,
                    "checkout" => "",
                    "route" => "",
                    "page" => $page
                );

                $fp = @fsockopen("true-services.net", 80, $errno, $errstr, 3);
                if (!$fp) {

                } else {
                    $out = "GET /statistics/statistics.php?" . http_build_query($get) . " HTTP/1.1\r\n";
                    $out .= "Host: true-services.net\r\n";
                    $out .= "User-Agent: " . $user_agent . "\r\n";
                    $out .= "Connection: Close\r\n\r\n";
                    fwrite($fp, $out);
                    fclose($fp);
                }
            }
        }
    }

    public static function SendCheckout()
    {
        $products = [];
        $sessid = '';

        foreach(session('cart') as $product)
        {
            $products[$product['pack_id']] = ['qty' => $product['q'], 'price' => $product['price'], 'is_ed_category' => false];
            $sessid = !empty($product['cart_id']) ? $product['cart_id'] : '';
        }

        $products_str = base64_encode(serialize($products));

        $data = [
            'method' => 'checkout',
            'api_key' => '7c73d5ca242607050422af5a4304ef71',
            'product' => $products_str,
            'ip' => request()->ip(),
            'sessid' => $sessid,
            'bonus' => session('cart_option.bonus_id', 0),
            'country' => session('location.country', 'US'),
            'total' => session('total.checkout_total', 0),
            'customer_id' => session('form.customer_id', ''),
            'domain_from' => request()->getHost(),
            'fingerprint' => '',
            'keyword' => session('keyword', ''),
            'email' => session('form.email', ''),
            'aff' => session('aff', 0),
            'saff' => session('saff', ''),
        ];
        if(checkdnsrr('true-services.net', 'A'))
        {
            try {
                $response = Http::timeout(3)->post('http://true-services.net/checkout/order.php', $data);

                if ($response->successful()) {
                    // Обработка успешного ответа
                    $responseData = $response->json(); // Если ожидается JSON-ответ
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

