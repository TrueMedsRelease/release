<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use App\Services\CurrencyServices;

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


Route::controller(SearchController::class)->group(function() {
    Route::post('/search', 'search_product')->name('search.search_product');
    Route::get('/search_autocomplete', 'search_autocomplete')->name('search.search_autocomplete');
    Route::get('/search/{search_text}', 'search_result')->name('search.search_result');
});

Route::controller(CartController::class)->group(function(){
    Route::get('/cart', 'index')->name('cart.index');
    Route::post('/cart_content', 'cart')->name('cart.content')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/add/{product}', 'add')->name('cart.add');
    Route::post('/cart/up', 'up')->name('cart.up')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/down', 'down')->name('cart.up')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/remove', 'remove')->name('cart.remove')->withoutMiddleware(VerifyCsrfToken::class);
    Route::post('/cart/upgrade', 'upgrade')->name('cart.upgrade')->withoutMiddleware(VerifyCsrfToken::class);
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
    Route::get('/lang={locale}', 'language')->name('home.language');
    Route::get('/curr={curr_code}', 'currency')->name('home.currency');
    Route::get('/first_letter/{letter}', 'first_letter')->name('home.first_letter');
    Route::get('/category/{category}', 'category')->name('home.category');
    Route::get('/active/{active}', 'active')->name('home.active');
    Route::get('disease/{disease}', 'disease')->name('home.disease');
    Route::get('/{product_name}', 'product')->name('home.product');
});





