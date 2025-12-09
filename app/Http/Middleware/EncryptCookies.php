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
        'js_stat_click_id',
        'js_stat_utm_source',
        'js_stat_utm_campaign',
        'js_stat_utm_term',
        'js_stat_utm_content',
        'js_stat_sub1',
        'js_stat_sub2',
        'tm_network_click_id',
        'tm_session_id',
        'tm_initial_referrer',
        'tm_visit_data'
    ];
}
