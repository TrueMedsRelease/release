<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

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
        'mxn' => 'MXN$',
        'nzd' => 'NZ$',
        'sgd' => 'S$',
        'zar' => 'R',
        'dop' => 'RD$',
        'krw' => '₩',
        'bam' => 'KM',
        'lkr' => 'Rs',
        'vnd' => 'đ',
        'myr' => 'RM',
        'hrk' => 'kn',
        'iqd' => 'د.ع',
        'sar' => 'ر.س',
        'qar' => 'ر.ق',
        'bbd' => 'BB$',
    ];

    /** @var array<string, array{prefix: string, space: bool}>|null */
    protected static $prefixSpaceMap = null;

    public static function ensurePrefixSpaceColumn(): bool
    {
        if (Schema::hasColumn('currency', 'prefix_space')) {
            return true;
        }

        try {
            Schema::table('currency', function (Blueprint $table) {
                $table->tinyInteger('prefix_space')->default(0);
            });
        } catch (\Throwable $e) {
            // Колонку уже добавил параллельный запрос или у пользователя БД нет прав ALTER.
        }

        return Schema::hasColumn('currency', 'prefix_space');
    }

    public static function prefixSpaceMap(): array
    {
        if (static::$prefixSpaceMap !== null) {
            return static::$prefixSpaceMap;
        }

        try {
            static::$prefixSpaceMap = Cache::rememberForever('currency_display_prefix_map', function () {
                $hasSpaceColumn = Schema::hasColumn('currency', 'prefix_space');
                $columns = $hasSpaceColumn ? ['code', 'prefix', 'prefix_space'] : ['code', 'prefix'];

                $map = [];
                foreach (Currency::query()->get($columns) as $currency) {
                    $map[strtolower($currency->code)] = [
                        'prefix' => (string) $currency->prefix,
                        'space' => $hasSpaceColumn && (int) $currency->prefix_space === 1,
                    ];
                }

                return $map;
            });
        } catch (\Throwable $e) {
            static::$prefixSpaceMap = [];
        }

        return static::$prefixSpaceMap;
    }

    public static function symbol(?string $code, string $default = '$'): string
    {
        $code = strtolower((string) $code);
        $settings = static::prefixSpaceMap()[$code] ?? null;

        $prefix = ($settings && trim($settings['prefix']) !== '')
            ? $settings['prefix']
            : (Currency::$prefix[$code] ?? $default);

        return ($settings && $settings['space']) ? $prefix . "\u{00A0}" : $prefix;
    }

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
        $coef             = session('currency_c', 1);
        $prefix           = Currency::symbol($current_currency);
        if ($round) {
            $total = ceil($number * $coef);
        } else {
            $total = round($number * $coef, 2);
        }

        if ($format) {
            return $prefix . number_format($total, 2, '.', '');
        } else {
            return $prefix . $total;
        }
    }

    public static function ConvertInEur($number, $round = false, $format = false)
    {
        $currency = 'eur';
        $coef             = Currency::query()->where('code', '=', $currency)->get()->toArray()[0]['coef'];
        $prefix           = Currency::symbol($currency);
        if ($round) {
            $total = ceil($number * $coef);
        } else {
            $total = round($number * $coef, 2);
        }

        if ($format) {
            return $prefix . number_format($total, 2, '.', '');
        } else {
            return $prefix . $total;
        }
    }

    public static function SumInCurrency($numbers = [], $round = false)
    {
        $current_currency = session('currency', 'usd');
        $sum              = 0;
        foreach ($numbers as $num) {
            $sum += floatval(preg_replace("/[^-0-9\.]/", "", $num));
        }

        if ($round) {
            $sum = round($sum);
        }

        return Currency::symbol($current_currency) . $sum;
    }

    public static function GetCurrencyByCountry($country)
    {
        $currency = Currency::query()->where('show_in_menu', '=', 1)->where(
            'country_iso2',
            'LIKE',
            '%' . $country . '%'
        )->first('code');
        if (empty($currency)) {
            $currencies = Currency::GetAllCurrency();

            if (count($currencies) > 1) {
                return config('app.currency');
            } else {
                if (count($currencies) == 1) {
                    return $currencies[0]['code'];
                } else {
                    return config('app.currency');
                }
            }
        } else {
            $currency = $currency->toArray();
            return $currency['code'];
        }
    }

    protected $table = 'currency';
}
