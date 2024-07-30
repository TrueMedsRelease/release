<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyService
{
    public static function GetAllCurrency()
    {
        $currency = Currency::query()->where('show_in_menu', '=', 1)->get()->toArray();

        return $currency;
    }

    public static function GetCoef($currency)
    {
        $currency = Currency::query()->where('code', '=', $currency)->get()->toArray();
        return $currency[0]['coef'];
    }

    public static function Convert($number, $round = false)
    {
        $current_currency = session('currency', 'usd');
        $coef = session('currency_c');
        $prefix = Currency::$prefix[$current_currency];
        if($round)
        {
            $total = ceil($number * $coef);
        }
        else
        {
            $total = round($number * $coef, 2);
        }
        return $prefix . $total;
    }
}