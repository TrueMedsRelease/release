<?php

return [
    'discount_percent' => 5,
    'window_hours'     => 24,

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
            'input_placeholder'      => 'Введите ваш email',
            'input_type'             => 'email',
            'button_text'            => 'Отправить код на почту',
        ],
        'push' => [
            'enabled' => true,
            'title'   => 'Push',
            'requires_identifier'    => true,   // identifier = user_push из cookie
            'normalize'              => null,
            'validate'               => null,
            'verify_with_identifier' => false,
            'input_placeholder'      => null,
            'button_text'            => 'Отправить код в Push',
            'hint'                   => 'Или введите код из полученного уведомления:',
        ],
        'telegram' => [
            'enabled'   => true, // включить, когда будет бот
            'title'     => 'Telegram',
            'requires_identifier'    => false, // токен сгенерирует сервер
            'normalize'              => null,
            'validate'               => null,
            'verify_with_identifier' => false,
            'input_placeholder'      => null,
            'button_text'            => 'Получить код в Telegram',
            'bot_url'                => 'https://t.me/test35253',
        ],
        // Пример будущего канала — просто раскомментировать/добавить:
        // 'viber' => ['enabled' => true, 'title' => 'Viber', 'requires_identifier' => true, ...],
    ],
];