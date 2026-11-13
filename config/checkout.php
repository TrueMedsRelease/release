<?php

/*
|--------------------------------------------------------------------------
| Checkout skins
|--------------------------------------------------------------------------
|
| Каждый "скин" чекаута — это набор флагов поверх одной и той же вьюшки
| (resources/views/checkout_content.blade.php). Новый вариант = новая запись
| с нужной комбинацией флагов, без копирования шаблона.
|
| Выбор скина (по приоритету):
|   1. ?checkout_skin=N в URL любой страницы — запоминается в сессии
|      (?checkout_skin=0 — сбросить на дефолт);
|   2. APP_CHECKOUT_SKIN в .env;
|   3. 1 (исходник).
|
| Доступные флаги:
|   email_first        — блок почты выше блока телефонов
|   soft_payments      — методы оплаты бледные, пока не заполнены контакты и
|                        адрес; при наведении подсказка; клик не переключает
|                        метод и не шлёт запросов, а ведёт к незаполненному полю
|   multistep          — пошаговый чекаут: Контакты и адрес → Оплата
|   journey_steps      — полоса прогресса ✓Магазин — ✓Корзина — Оформление — Оплата.
|                        С multistep следует за шагами; без него прилипает сверху и
|                        сама переключается на "Оплату", когда долистали до неё
|   collapse_optional  — альтернативные телефон/почта спрятаны за ссылкой
|   numbered_sections  — нумерация секций (1, 2, 3...)
|   pay_total_button   — сумма заказа прямо на кнопке оплаты
|   large_ui           — крупный контрастный интерфейс: шрифты, поля, плитки
|                        оплаты, кнопки, заметный фокус; подсказка у оплаты
|                        видна всегда, а не только при наведении
|   single_column      — одно поле в строке
|   inline_errors      — ошибки текстом под полем, а не пузырём над ним
|   autofill           — разрешить автозаполнение браузера (autocomplete)
|   valid_marks        — зелёная галочка у правильно заполненного поля
|   help_phone         — "Нужна помощь? Позвоните нам" у блока оплаты
|   validate_on_blur   — проверка поля сразу после ухода из него
|   email_typo_hint    — подсказка опечатки в почте: "gmial.con → gmail.com?"
|   trust_badges       — блок "Безопасная оплата" с логотипами под кнопкой оплаты
|   hide_timer         — без таймера обратного отсчёта
|
*/

return [

    'default_skin' => (int) env('APP_CHECKOUT_SKIN', 1),

    'switcher' => env('APP_CHECKOUT_SKIN_SWITCHER', env('APP_DEBUG', false)),

    'skins' => [
        1 => [
            'name'  => 'Original',
            'flags' => [],
        ],
        2 => [
            'name'  => 'Email first + soft payments',
            'flags' => ['email_first', 'soft_payments'],
        ],
        3 => [
            'name'  => 'Multistep',
            'flags' => ['email_first', 'soft_payments', 'multistep', 'journey_steps', 'pay_total_button'],
        ],
        4 => [
            // Для аудитории постарше: крупно, по одному полю в строке, понятные ошибки.
            'name'  => 'Focus (easy)',
            'flags' => [
                'email_first', 'soft_payments', 'collapse_optional', 'numbered_sections', 'pay_total_button',
                'large_ui', 'single_column', 'inline_errors', 'autofill', 'valid_marks', 'help_phone',
            ],
        ],
        5 => [
            // Пошагово (данные → оплата) с полосой прогресса и защитой от ошибок ввода:
            // крупно, по одному полю в строке, проверка сразу, подсказка опечатки в почте,
            // без таймера, с блоком безопасной оплаты у кнопки.
            'name'  => 'Smart',
            'flags' => [
                'email_first', 'soft_payments', 'collapse_optional', 'pay_total_button',
                'large_ui', 'single_column', 'inline_errors', 'autofill', 'valid_marks', 'help_phone',
                'multistep', 'journey_steps', 'validate_on_blur', 'email_typo_hint', 'trust_badges', 'hide_timer',
            ],
        ],
    ],

];
