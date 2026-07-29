<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MedBot API Configuration
    |--------------------------------------------------------------------------
    */

    'enabled' => env('MEDBOT_ENABLED', true),

    'api_key' => env('APP_BOT_KEY'),

    'base_url' => env('MEDBOT_BASE_URL', 'http://pills-333.com'),

    'timeout' => 15,

    'connect_timeout' => 3,

    'max_results' => 30,

    'poll_interval' => 5000,

];
