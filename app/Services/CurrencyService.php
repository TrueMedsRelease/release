<?php

namespace App\Services;

use App\Models\Currency;

class CurrencySetvice
{
    public static function Convert($number)
    {
        $current_currency = session('currency', 'usd');
        $currency = Currency::query()->where('show_in_menu', '=', 1)->where('code', '=', $current_currency)->get()->toArray();
        $currency = $currency[0];

        return $currency['prefix'] . ($number * $currency['coef']);
    }
}