<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Cookie;

class CurrencyServices{
    public static function getAllCurrencies() {
        $currencies = Currency::query()
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();

        return $currencies;
    }

    public static function getCoef($cur_id){
		return Currency::query()->where('id', '=', $cur_id)->value('coef');
	}

    public static function upgrade($cost) {
        if (Cookie::has('curr')) {
            $cur_currency = Cookie::get('curr');
        } else {
            $cur_currency = config('app.currency');
        }

		$coeff = Currency::query()->where('code', '=', $cur_currency)->value('coef');

		return number_format($cost * $coeff, 2, '.', '');
	}

    public static function upgradeCeil($cost) {
        if (Cookie::has('curr')) {
            $cur_currency = Cookie::get('curr');
        } else {
            $cur_currency = config('app.currency');
        }

		$coeff = Currency::query()->where('code', '=', $cur_currency)->value('coef');
		return number_format(ceil($cost * $coeff), 0, '.', '');
	}

    static public function format($cost) {
        if (Cookie::has('curr')) {
            $cur_currency = Cookie::get('curr');
        } else {
            $cur_currency = config('app.currency');
        }

		$currency_prefix = Currency::query()->where('code', '=', $cur_currency)->value('prefix');
		return $currency_prefix . $cost;
	}
}