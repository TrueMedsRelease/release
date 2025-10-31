<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        'js_stat_aff_id',
        'js_stat_design_id',
        'tm_session_id',
        'tm_initial_referrer',
        'tm_visit_data'
    ];
}
