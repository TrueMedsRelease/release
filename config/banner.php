<?php

/*
 * Большой баннер в шапке (блок .christmas).
 *
 * Включить:  APP_HEADER_BANNER=christmas   (в .env, затем php artisan config:clear)
 * Выключить: APP_HEADER_BANNER=            (пусто или off)
 *
 * Картинки берутся из public/pub_images/{баннер}_{biggest|big|middle|small}.png
 * Новый баннер = положить 4 картинки и добавить его имя в 'available'.
 */

return [
    'header' => env('APP_HEADER_BANNER', ''),

    'available' => [
        'pay',
        'black_friday',
        'christmas',
        'new_year',
        'valentine_day',
    ],
];
