<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

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

        $get = array(
            "sessid" => session('_token'),
            "dt" => date("Y-m-d H:i:s"),
            "ip" => request()->ip(),
            "is_uniq" => $is_uniq,
            "country" => session('location.country'),
            "aff" => session('aff', 0),
            "saff" => session('saff', ''),
            "domain_from" => parse_url(config('app.url'), PHP_URL_HOST),
            "ref" => session('referer', ''),
            "keyword" => session('keyword', ''),
            "user_agent" => request()->userAgent(),
            "store_skin" => $design,
            "theme" => "",
            "duration" => 0,
            "checkout" => "",
            "route" => "",
            "page" => $page
        );
        // $client = new Client();
        // $promise = $client->getAsync('http://true-services.net/statistics/statistics.php?' . http_build_query($get));
        // $promise->then(
        //     function ($response) {
        //         session(['response' => $response]);
        //     },
        //     function ($exception) {
        //         session(['exception' => $exception]);
        //     }
        // );

        // $promise = Http::async()->get('http://true-services.net/statistics/statistics.php?' . http_build_query($get))->then(function($response) {
        //     dump($response->body());
        // });

        $fp = fsockopen("true-services.net", 80, $errno, $errstr, 30);
        if (!$fp) {
            echo "$errstr ($errno)\n";
        } else {
            $out = "GET /statistics/statistics.php?" . http_build_query($get) . " HTTP/1.1\r\n";
            $out .= "Host: true-services.net\r\n";
            $out .= "User-Agent: " . request()->userAgent() . "\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            fclose($fp);        
        }
    }

}

