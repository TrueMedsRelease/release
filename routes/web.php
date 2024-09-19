<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Currency;
use App\Services\GeoIpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

if(!session()->has('location'))
{
    session(['location' => GeoIpService::GetInfoByIp()]);
}

if(!session()->has('currency'))
{
    $currency = config('app.currency');
    $coef = Currency::GetCoef($currency);
    session(['currency' => $currency]);
    session(['currency_c' => $coef]);
}

if(!session()->has('referer'))
{
    $request = new Request();
    if(!empty($request->referer))
    {
        session(['referer' => $request->referer]);
    }
    else
    {
        session(['referer' => '']);
    }
}

if(!session()->has('aff'))
{
    if(!empty(request('aff')))
    {
        session(['aff' => request('aff')]);
    }
    else
    {
        session(['aff' => config('app.aff')]);
    }
}

if(!session()->has('saff'))
{
    if(!empty(request('saff')))
    {
        session(['saff' => request('saff')]);
    }
}

if(!session()->has('keyword'))
{
    if(!empty(request('keyword')))
    {
        session(['keyword' => request('keyword')]);
    }
}

if(!session()->has('refc'))
{
    if(!empty(request('refc')))
    {
        session(['refc' => request('refc')]);
    }
}

if(!session()->has('coupon_get'))
{
    if(!empty(request('coupon')))
    {
        session(['coupon_get' => request('coupon')]);
    }
}

Route::controller(SearchController::class)->group(function() {
    Route::post('/search', 'search_product')->name('search.search_product');
    Route::get('/search_autocomplete', 'search_autocomplete')->name('search.search_autocomplete');
    Route::get('/search/{search_text}', 'search_result')->name('search.search_result');
});

Route::controller(CartController::class)->group(function(){
    Route::get('/cart', 'index')->name('cart.index');
    Route::get('/cart_content', 'cart')->name('cart.content')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/add/{product}', 'add')->name('cart.add');
    Route::post('/cart/up', 'up')->name('cart.up')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/down', 'down')->name('cart.up')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/remove', 'remove')->name('cart.remove')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/upgrade', 'upgrade')->name('cart.upgrade')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/change-shipping', 'change_shipping')->name('cart.shipping')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/change-bonus', 'change_bonus')->name('cart.bonus')->withoutMiddleware(VerifyCsrfToken::class);
});

Route::controller(CheckoutController::class)->group(function () {
    Route::get('/checkout', 'index')->name('checkout.index');
    Route::get('/checkout_content', 'checkout')->name('checkout.content')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/checkout/insurance', 'insurance')->name('checkout.insurance')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/checkout/secret_package', 'secret_package')->name('checkout.secret_package')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/checkout/change-shipping', 'change_shipping')->name('checkout.shipping')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/checkout/coupon', 'coupon')->name('checkout.coupon');
    Route::post('/checkout/order', 'order')->name('checkout.order');
    Route::post('/checkout/auth', 'auth')->name('checkout.auth');
    Route::post('/checkout/change_country', 'change_country')->name('checkout.country')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/crypto_info', 'crypto_info')->name('checkout.crypto_info')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/validate_for_crypt', 'validate_for_crypt')->name('checkout.validate_for_crypt');
    Route::post('/paypal', 'paypal')->name('checkout.paypal');
    Route::post('/check_payment', 'check_payment')->name('checkout.check_payment')->withoutMiddleware(VerifyCsrfToken::class);
    Route::get('/complete', 'complete')->name('checkout.complete');
});

Route::controller(HomeController::class)->group(function() {
    Route::get('/', 'index')->name('home.index');
    Route::get('/about', 'about')->name('home.about');
    Route::get('/contact_us', 'contact_us')->name('home.contact_us');
    Route::get('/affiliate', 'affiliate')->name('home.affiliate');
    Route::get('/help', 'help')->name('home.help');
    Route::get('/testimonials', 'testimonials')->name('home.testimonials');
    Route::get('/delivery', 'delivery')->name('home.delivery');
    Route::get('/moneyback', 'moneyback')->name('home.moneyback');
    Route::get('/login', 'login')->name('home.login');
    Route::get('/lang={locale}', 'language')->name('home.language');
    Route::get('/curr={currency}', 'currency')->name('home.currency');
    Route::get('/first_letter/{letter}', 'first_letter')->name('home.first_letter');
    Route::get('/category/{category}', 'category')->name('home.category');
    Route::get('/active/{active}', 'active')->name('home.active');
    Route::get('disease/{disease}', 'disease')->name('home.disease');
    Route::get('/product/{product_name}', 'product')->name('home.product');
    Route::get('/design={design}', 'design')->name('home.design');
    Route::post('/request_call', 'request_call')->name('home.request_call')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/request_subscribe', 'request_subscribe')->name('home.request_subscribe')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/request_contact_us', 'request_contact_us')->name('home.request_contact_us')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/request_affiliate', 'request_affiliate')->name('home.request_affiliate')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/request_login', 'request_login')->name('home.request_login')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/check_code', 'check_code')->name('home.check_code')->withoutMiddleware(VerifyCsrfToken::class);
});
