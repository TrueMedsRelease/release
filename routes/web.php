<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SearchController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Currency;
use App\Models\Language;
use App\Services\GeoIpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;

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


// if (isset($_GET['design']) && $_GET['design']) {
//     $design = $_GET['design'] ? $_GET['design'] : config('app.design');
//     unset($_GET['design']);

//     request()->route('home.design_with_url', ['any_url' => request()->server('REQUEST_URI'), 'design' => $design]);
// }

// if (isset($_GET['lang']) && $_GET['lang']) {
//     $lang = $_GET['lang'] ? $_GET['lang'] : App::currentLocale();
//     unset($_GET['lang']);

//     request()->route('home.language_with_url', ['any_url' => request()->server('REQUEST_URI'), 'locale' => $lang]);
// }

// if (isset($_GET['curr']) && $_GET['curr']) {
//     $curr = $_GET['curr'] ? $_GET['curr'] : config('app.currency');
//     unset($_GET['curr']);

//     request()->route('home.currency_with_url', ['any_url' => request()->server('REQUEST_URI'), 'currency' => $curr]);
// }

Route::controller(SearchController::class)->group(function() {
    Route::post('/search', 'search_product')->name('search.search_product')->withoutMiddleware(VerifyCsrfToken::class);
    Route::get('/search_autocomplete', 'search_autocomplete')->name('search.search_autocomplete');
    Route::get('/search/{search_text}', 'search_result')->name('search.search_result');
    Route::get('/app/search.php', 'search_for_aff')->name('search.searh_for_aff');
});

Route::controller(CartController::class)->group(function(){
    Route::get('/cart', 'index')->name('cart.index');
    Route::get('/cart_content', 'cart')->name('cart.content')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/add/{product}', 'add')->name('cart.add')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/up', 'up')->name('cart.up')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/down', 'down')->name('cart.down')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/remove', 'remove')->name('cart.remove')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/upgrade', 'upgrade')->name('cart.upgrade')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/change-shipping', 'change_shipping')->name('cart.shipping')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/change-bonus', 'change_bonus')->name('cart.bonus')->withoutMiddleware(VerifyCsrfToken::class);
    Route::get('/cart/add_pack/{pack_id}', 'add_pack')->name('cart.add_pack');
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
    Route::post('/data_for_crypt', 'data_for_crypt')->name('checkout.data_for_crypt')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/validate_for_google', 'validate_for_google')->name('checkout.validate_for_google');
    Route::post('/validate_for_sepa', 'validate_for_sepa')->name('checkout.validate_for_sepa');
    Route::post('/send_google', 'send_google')->name('checkout.send_google')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/log_google', 'log_google')->name('checkout.log_google')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/paypal', 'paypal')->name('checkout.paypal');
    Route::post('/sepa', 'sendSepa')->name('checkout.sendSepa');
    Route::post('/check_payment', 'check_payment')->name('checkout.check_payment')->withoutMiddleware(VerifyCsrfToken::class);
    Route::get('/complete', 'complete')->name('checkout.complete');
    Route::post('/send_checkout_phone_email', 'send_checkout_phone_email')->name('checkout.send_checkout_phone_email')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/zelle_data', 'zelleData')->name('checkout.zelleData')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/zelle', 'zelle')->name('checkout.zelle')->withoutMiddleware(VerifyCsrfToken::class);
});

Route::get('/redirect', function () {
    if(!empty(session('order.url')))
    {
        return redirect()->to(session('order.url'));
    }
})->name('redirect_url');

Route::controller(HomeController::class)->group(function() {
    Route::get('/', 'index')->name('home.index');
    Route::get('/{product_name}.html', 'product')->name('home.product')->withoutMiddleware(VerifyCsrfToken::class);
    Route::get('/{product_name}.html/landing={landing}', 'product_landing')->name('home.product_landing')->withoutMiddleware(VerifyCsrfToken::class);
    Route::get('/about_us{other_url?}', 'about')->name('home.about');
    Route::get('/contact_us{other_url?}', 'contact_us')->name('home.contact_us');
    Route::get('/affiliate{other_url?}', 'affiliate')->name('home.affiliate');
    Route::get('/faq{other_url?}', 'help')->name('home.help');
    Route::get('/testimonials{other_url?}', 'testimonials')->name('home.testimonials');
    Route::get('/shipping{other_url?}', 'delivery')->name('home.delivery');
    Route::get('/moneyback{other_url?}', 'moneyback')->name('home.moneyback');
    Route::get('/login', 'login')->name('home.login');
    Route::get('/lang={locale}', 'language')->name('home.language');
    Route::get('/curr={currency}', 'currency')->name('home.currency');
    Route::get('/{any_url}/lang={locale}', 'language_with_url')->name('home.language_with_url')->where('any_url', '(?!string1|string2)[^\/]+');
    Route::get('/{any_url}/curr={currency}', 'currency_with_url')->name('home.currency_with_url')->where('any_url', '(?!string1|string2)[^\/]+');
    Route::get('/first_letter/{letter}', 'first_letter')->name('home.first_letter');
    Route::get('/category/{category}', 'category')->name('home.category');
    Route::get('/active/{active}', 'active')->name('home.active');
    Route::get('/disease/{disease}', 'disease')->name('home.disease');
    Route::get('/design={design}', 'design')->name('home.design');
    Route::get('/{any_url}/design={design}', 'design_with_url')->name('home.design_with_url')->where('any_url', '(?!string1|string2)[^\/]+');
    Route::post('/request_call', 'request_call')->name('home.request_call')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/request_subscribe', 'request_subscribe')->name('home.request_subscribe')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/request_contact_us', 'request_contact_us')->name('home.request_contact_us')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/request_affiliate', 'request_affiliate')->name('home.request_affiliate')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/request_login', 'request_login')->name('home.request_login')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/check_code', 'check_code')->name('home.check_code')->withoutMiddleware(VerifyCsrfToken::class);
    Route::get('/set_images/{pill}', 'set_images')->name('home.set_images');
    Route::post('/pwa/pwa_info', 'pwa_info')->name('home.pwa_info')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/push/save_push', 'save_push_data')->name('home.save_push_data')->withoutMiddleware(VerifyCsrfToken::class);
    Route::get('/check_landing', 'check_landing')->name('home.check_landing');
    Route::get('/checkup', 'checkup')->name('home.checkup');
    Route::get('/sitemap{other_url?}', 'sitemap')->name('home.sitemap');
    Route::get('/error_page', 'errorPage')->name('home.error_page');
});

Route::controller(AdminController::class)->group(function() {
    Route::get('/admin/logout', 'admin_logout')->name('admin.admin_logout');

    Route::get('/admin', 'admin_enter')->name('admin.admin_enter');
    Route::get('/admin/login', 'admin_login')->name('admin.admin_login');
    Route::post('/admin/request_login', 'request_login')->name('admin.request_login')->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/admin/main_page', 'index')->name('admin.index');
    Route::get('/admin/admin_main_page', 'admin_main_content')->name('admin.admin_main_content')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/main_page/add_to_main', 'add_to_main')->name('admin.add_to_main')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/main_page/delete_from_main', 'delete_from_main')->name('admin.delete_from_main')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/main_page/product_up_in_sort', 'product_up_in_sort')->name('admin.product_up_in_sort')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/main_page/product_down_in_sort', 'product_down_in_sort')->name('admin.product_down_in_sort')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/main_page/save_subscribe_info', 'save_subscribe_info')->name('admin.save_subscribe_info')->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/admin/seo', 'admin_seo')->name('admin.admin_seo');
    Route::get('/admin/seo_content', 'admin_seo_content')->name('admin.admin_seo_content')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/seo/load_pixel', 'load_pixel')->name('admin.load_pixel')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/seo/save_pixel', 'save_pixel')->name('admin.save_pixel')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/seo/delete_pixel', 'delete_pixel')->name('admin.delete_pixel')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/seo/load_product_url', 'load_product_url')->name('admin.load_product_url')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/seo/save_product_url', 'save_product_url')->name('admin.save_product_url')->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/admin/main_properties', 'main_properties')->name('admin.main_properties');
    Route::post('/admin/main_properties/save_user_properties', 'save_user_properties')->name('admin.save_user_properties')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/main_properties/load_page_properties', 'load_page_properties')->name('admin.load_page_properties')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/main_properties/save_page_properties', 'save_page_properties')->name('admin.save_page_properties')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/main_properties/save_template', 'save_template')->name('admin.save_template')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/main_properties/save_default_error_page', 'save_default_error_page')->name('admin.save_default_error_page')->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/admin/available_products', 'available_products')->name('admin.available_products');
    Route::get('/admin/available_products_content', 'available_products_content')->name('admin.available_products_content')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/available_products/add_to_showed', 'add_to_showed')->name('admin.add_to_showed')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/available_products/delete_from_showed', 'delete_from_showed')->name('admin.delete_from_showed')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/available_products/gift_card_info', 'gift_card_info')->name('admin.gift_card_info')->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/admin/available_packagings', 'available_packagings')->name('admin.available_packagings');
    Route::get('/admin/available_packagings_content', 'available_packagings_content')->name('admin.available_packagings_content')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/available_packagings/load_packaging_info', 'load_packaging_info')->name('admin.load_packaging_info')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/available_packagings/add_pack_to_showed', 'add_pack_to_showed')->name('admin.add_pack_to_showed')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/available_packagings/delete_pack_from_showed', 'delete_pack_from_showed')->name('admin.delete_pack_from_showed')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/available_packagings/packaging_up_in_sort', 'packaging_up_in_sort')->name('admin.packaging_up_in_sort')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/available_packagings/packaging_down_in_sort', 'packaging_down_in_sort')->name('admin.packaging_down_in_sort')->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/admin/products', 'products')->name('admin.products');
    Route::get('/admin/products_content', 'products_content')->name('admin.products_content')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/products/load_product_info', 'load_product_info')->name('admin.load_product_info')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/admin/products/save_product_info', 'save_product_info')->name('admin.save_product_info')->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/admin/languages', 'admin_languages')->name('admin.admin_languages');
    Route::post('/admin/save_languages_info', 'save_languages_info')->name('admin.save_languages_info')->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/admin/currencies', 'admin_currencies')->name('admin.admin_currencies');
    Route::post('/admin/save_currencies_info', 'save_currencies_info')->name('admin.save_currencies_info')->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/admin/checkout', 'admin_checkout')->name('admin.admin_checkout');
    Route::post('/admin/checkout/save_checkout_info', 'save_checkout_info')->name('admin.save_checkout_info')->withoutMiddleware(VerifyCsrfToken::class);
});

Route::fallback(function () {
    if (env('APP_ERROR_PAGE')) {
        return response()->view('404', [
            'design' => session('design', config('app.design')),
        ], 404);
    } else {
        $redirect_url = request()->server('REQUEST_URI');
        $index = strripos($redirect_url, '/');
        $redirect_url = substr($redirect_url, 0, $index);

        return redirect($redirect_url);
    }
});

Route::options('/{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');