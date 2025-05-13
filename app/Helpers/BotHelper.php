<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BotHelper
{
    public static $botList = [
        'googlebot',
        'bingbot',
        'yandex',
        'slurp',
        'duckduckbot',
        'baiduspider',
        'sogou',
        'exabot',
        'facebot',
        'ia_archiver',
        'ahrefsbot',
        'semrushbot',
        'mj12bot',
        'dotbot',
        'rogerbot',
        'megaindex',
        'blexbot',
        'yoozbot',
        'bot',
        'spider',
        'PetalBot'
    ];

    public static function IsUserAgentBot(): bool
    {
        $userAgent = Str::lower(request()->userAgent());

        return Arr::exists(static::$botList, $userAgent);
    }

}