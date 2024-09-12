<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    public static $prefix = [
        'usd' => '$',
        'eur' => '€',
        'aud' => 'A$',
        'gbp' => '£',
        'jpy' => '¥',
        'cny' => '圓',
        'cad' => 'C$',
        'hkd' => 'HK$',
        'aed' => 'د.إ',
        'chf' => '₣',
        'pln' => 'zł',
        'czk' => 'Kč',
        'bgn' => 'лв.',
        'huf' => 'Ft',
        'dkk' => 'kr.',
        'nok' => 'NKr',
        'sek' => 'kr',
        'ron' => 'L',
        'brl' => 'R$',
        'ars' => 'ARG$',
        'php' => '₱',
        'mxn' => 'MXN$'
    ];

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

    public static function Convert($number, $round = false, $format = false)
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

        if ($format) {
            return $prefix . number_format($total, 2, '.', '');
        } else {
            return $prefix . $total;
        }

    }

    public static function SumInCurrency($numbers = [])
    {
        $current_currency = session('currency', 'usd');
        $sum = 0;
        foreach($numbers as $num)
        {
            $sum += floatval(preg_replace("/[^-0-9\.]/","",$num));
        }

        return Currency::$prefix[$current_currency] . $sum;
    }

    protected $table = 'currency';
}
