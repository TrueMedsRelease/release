<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BotHelper
{
    public static array $botList = [
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

        foreach (static::$botList as $bot) {
            if (Str::contains($userAgent, $bot)) {
                return true;
            }
        }

        return false;
    }

}