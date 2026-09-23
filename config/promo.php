<?php

return [
    'discount_percent' => 5,
    'window_hours'     => 24,

    'promo_coupon_enabled' => filter_var(env('APP_PROMO_COUPON_POPUP', true), FILTER_VALIDATE_BOOLEAN),

    'true_serv' => [
        'subscribe_url' => 'https://true-serv.net/support/messages/promo_subscribe.php',
        'verify_url'    => 'https://true-serv.net/support/messages/promo_verify.php',
    ],

    /*
     * РЕЕСТР КАНАЛОВ. Новый канал = добавить массив сюда + строку в promo_channels.php на true-serv.
     * requires_identifier      — клиент обязан передать identifier
     * normalize                — 'lower' | null
     * validate                 — 'email' | null
     * verify_with_identifier   — повторять identifier при проверке кода
     * input_placeholder        — показать инпут в виджете (null — не показывать)
     */

    'channels' => [
        'email' => [
            'enabled' => true,
            'title'   => 'Email',
            'requires_identifier'    => true,
            'normalize'              => 'lower',
            'validate'               => 'email',
            'verify_with_identifier' => true,
            'input_placeholder'      => 'email_ph',
            'input_type'             => 'email',
            'button_text'            => 'email_btn',
        ],
        'push' => [
            'enabled' => true,
            'title'   => 'Push',
            'requires_identifier'    => true,   // identifier = user_push из cookie
            'normalize'              => null,
            'validate'               => null,
            'verify_with_identifier' => false,
            'input_placeholder'      => null,
            'button_text'            => 'push_btn',
            'hint'                   => 'push_hint',
        ],
        'telegram' => [
            'enabled'   => false, // включить, когда будет бот
            'title'     => 'Telegram',
            'requires_identifier'    => false, // токен сгенерирует сервер
            'normalize'              => null,
            'validate'               => null,
            'verify_with_identifier' => false,
            'input_placeholder'      => null,
            'button_text'            => 'bot_btn',
            'bot_url'                => 'https://t.me/test35253',
        ],
        'whatsapp' => [
            'enabled' => false,
            'title'   => 'WhatsApp',
            'requires_identifier'    => false,
            'normalize'              => null,
            'validate'               => null,
            'verify_with_identifier' => false,
            'input_placeholder'      => null,
            'button_text'            => 'bot_btn',
            'bot_url'                => '', // пример: https://wa.me/15551234567
        ],
        'facebook' => [
            'enabled' => false,
            'title'   => 'Facebook',
            'requires_identifier'    => false,
            'normalize'              => null,
            'validate'               => null,
            'verify_with_identifier' => false,
            'input_placeholder'      => null,
            'button_text'            => 'bot_btn',
            'bot_url'                => '', // пример: https://m.me/YourPage
        ],
        'instagram' => [
            'enabled' => false,
            'title'   => 'Instagram',
            'requires_identifier'    => false,
            'normalize'              => null,
            'validate'               => null,
            'verify_with_identifier' => false,
            'input_placeholder'      => null,
            'button_text'            => 'bot_btn',
            'bot_url'                => '', // пример: https://ig.me/m/yourprofile
        ],
        'discord' => [
            'enabled' => false,
            'title'   => 'Discord',
            'requires_identifier'    => false,
            'normalize'              => null,
            'validate'               => null,
            'verify_with_identifier' => false,
            'input_placeholder'      => null,
            'button_text'            => 'bot_btn',
            'bot_url'                => '', // пример: https://discord.gg/yourinvite
        ],
        // Пример будущего канала — просто раскомментировать/добавить:
        // 'viber' => ['enabled' => true, 'title' => 'Viber', 'requires_identifier' => true, ...],
    ],
];