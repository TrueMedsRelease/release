<?php

use App\Http\Controllers\HomeController;
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

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/about', [HomeController::class, 'about'])->name('home.about');
Route::get('/help', [HomeController::class, 'help'])->name('home.help');
Route::get('/testimonials', [HomeController::class, 'testimonials'])->name('home.testimonials');
Route::get('/delivery', [HomeController::class, 'delivery'])->name('home.delivery');
Route::get('/moneyback', [HomeController::class, 'moneyback'])->name('home.moneyback');
Route::get('/lang={locale}', [HomeController::class, 'language'])->name('home.language');
Route::get('/first_letter/{letter}', [HomeController::class, 'first_letter'])->name('home.first_letter');
Route::get('/{product_name}', [HomeController::class, 'product'])->name('home.product');

