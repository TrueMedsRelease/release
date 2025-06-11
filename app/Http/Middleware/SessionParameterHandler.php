<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\Currency;
use App\Models\Language;
use App\Services\GeoIpService;

class SessionParameterHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // location
        if (!session()->has('location')) {
            session(['location' => GeoIpService::GetInfoByIp()]);

            if (!empty(session('location.country'))) {
                $curr = Currency::GetCurrencyByCountry(session('location.country'));
                $coef = Currency::GetCoef($curr);
                session(['currency' => $curr]);
                session(['currency_c' => $coef]);

                $lang = Language::GetLanguageByCountry(session('location.country'));
                App::setLocale($lang);
            }
        }

        // referer
        if (!session()->has('referer')) {
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            session(['referer' => $referer]);
        }

        // aff
        if (!empty($request->query('aff'))) {
            session(['aff' => $request->query('aff')]);
        } else {
            session(['aff' => config('app.aff')]);
        }

        // saff
        if (!empty($request->query('saff'))) {
            session(['saff' => $request->query('saff')]);
        }

        // keyword
        if (!empty($request->query('keyword'))) {
            session(['keyword' => $request->query('keyword')]);
        }

                // refc
        if (!empty($request->query('refc'))) {
            session(['refc' => $request->query('refc')]);
        }

        // coupon_get
        if (!empty($request->query('coupon'))) {
            session(['coupon_get' => $request->query('coupon')]);
        }

        // lang (перезаписываем всегда при наличии)
        if (!empty($request->query('lang'))) {

            $lang = strtolower($request->query('lang'));

            if (isset(Language::$languages[$lang])) {

                session(['locale' => $lang]);
                App::setLocale($request->query('lang'));

            } else {

                session(['locale' => env('APP_LANGUAGE')]);
                App::setLocale(env('APP_LANGUAGE'));
            }
        }

        // curr (перезаписываем всегда при наличии)
        if (!empty($request->query('curr'))) {

            $curr = strtolower($request->query('curr'));

            if (isset(Currency::$prefix[$curr])) {

                $coef = Currency::GetCoef($curr);
                session(['currency' => $curr]);
                session(['currency_c' => $coef]);

            } else {
                $coef = Currency::GetCoef(env('APP_CURRENCY'));
                session(['currency' => env('APP_CURRENCY')]);
                session(['currency_c' => $coef]);
            }
        }

        // design (перезаписываем всегда при наличии)
        if (!empty($request->query('design'))) {
            if (in_array($request->query('design'), [1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 12])) {
                session(['design' => 'design_' . $request->query('design')]);
            }
        }

        return $next($request);
    }
}
