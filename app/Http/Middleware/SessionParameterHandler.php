<?php

namespace App\Http\Middleware;

use App\Http\Controllers\CartController;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductPackaging;
use App\Services\GeoIpService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

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
                session(['locale' => $lang]);
            }
        }

        // referer
        if (!session()->has('referer')) {
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            session(['referer' => $referer]);
        }

        $on7Pills = Str::is(['7-pills.com', '*.7-pills.com', '7-pills.net', '*.7-pills.net', '7-pill.com', '*.7-pill.com', '77-pills.com', '*.77-pills.com', '7-pillz.com', '*.7-pillz.com', '77-pillz.com', '*.77-pillz.com', '777-pills.com', '*.777-pills.com'], $request->getHost());

        // aff
        if (!empty($request->query('aff'))) {
            if ($on7Pills) {
                session(['aff' => 0]);
            } else {
                session(['aff' => $request->query('aff')]);
            }

            if (config('app.aff') == 0 && !$on7Pills) {
                Cookie::queue(
                    Cookie::make(
                        'AFF_ID',
                        $request->query('aff'),
                        60 * 24 * 365,
                        '/',
                        $request->getHost(),
                        config('session.secure'),
                        true,
                        false,
                        config('session.same_site', 'lax')
                    )
                );
            }
        } elseif (!session()->has('aff')) {
            if ($on7Pills) {
                session(['aff' => 0]);
            } else {
                $affFromCookie = $request->cookie('AFF_ID');
                session(['aff' => $affFromCookie ?? config('app.aff')]);
            }
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
            if (in_array($request->query('design'), [1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 12, 14])) {
                session(['design' => 'design_' . $request->query('design')]);
            }
        }

        if (!empty($request->query('product'))) {
            if (in_array($request->query('product'), ['tretiva', 'cialis'])) {
                if ($request->query('product') == 'tretiva') {
                    return redirect(route('home.product', 'accutane'));
                } else {
                    return redirect(route('home.product', 'cialis'));
                }
            }
        }

        if (!empty($request->query('buy_pack'))) {
            $pack_id = $request->query('buy_pack');

            if ($pack_id) {
                $product_pack = ProductPackaging::query()->find($pack_id);
                $product      = Product::query()->find($product_pack->product_id);

                if ($product->is_showed == 1 && $product_pack->is_showed == 1) {
                    CartController::add_pack($pack_id);
                    return redirect(route('cart.index'));
                } else {
                    if (env('APP_ERROR_PAGE')) {
                        return response()->view('404', ['design' => session('design', config('app.design'))], 404);
                    } else {
                        return redirect(route('home.index'));
                    }
                }
            }
        }

        return $next($request);
    }
}
