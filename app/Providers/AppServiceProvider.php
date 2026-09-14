<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!env('APP_DEBUG')) {
            URL::forceScheme('https');
        }

        View::share('global_promo_discount', session('promo_discount', 0));
        View::share('global_promo_code', session('promo_code', ''));
    }
}
