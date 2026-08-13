<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class TrueServService
{
    private const HOST = 'true-serv.net';
    private const KEY  = 'true_serv_dns_ok';

    public static function available(): bool
    {
        return (bool) Cache::get(self::KEY, false);
    }

    public static function refresh(): bool
    {
        $ok = checkdnsrr(self::HOST, 'A');
        Cache::put(self::KEY, $ok, now()->addMinutes($ok ? 10 : 2));
        return $ok;
    }
}