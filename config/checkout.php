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
|                        адрес; при наведении подсказка; клик работает как раньше
|   multistep          — пошаговый чекаут: Контакты → Адрес → Оплата
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
            'flags' => ['email_first', 'soft_payments', 'multistep', 'pay_total_button'],
        ],
        4 => [
            // Для аудитории постарше: крупно, по одному полю в строке, понятные ошибки.
            'name'  => 'Focus (easy)',
            'flags' => [
                'email_first', 'soft_payments', 'collapse_optional', 'numbered_sections', 'pay_total_button',
                'large_ui', 'single_column', 'inline_errors', 'autofill', 'valid_marks', 'help_phone',
            ],
        ],
    ],

];
