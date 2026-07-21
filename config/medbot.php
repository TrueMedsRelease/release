<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MedBot API Configuration
    |--------------------------------------------------------------------------
    |
    | Конфигурация для AI-поиска препаратов через MedBot API.
    | API не требует аутентификации, только rate limit 6 запросов/минуту на IP.
    |
    */

    'enabled' => env('MEDBOT_ENABLED', true),

    'base_url' => env('MEDBOT_BASE_URL', 'http://pills-333.com'),

    'timeout' => 15,

    'connect_timeout' => 3,

    'max_results' => 30,

];
