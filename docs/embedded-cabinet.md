# Встроенный личный кабинет (My orders внутри шопа)

Кнопка «My Account» ведёт покупателя либо на внешний true-help (как раньше),
либо во встроенный кабинет шопа — переключается в админке:
**Customer Cabinet** (`/admin/cabinet`).

## Как это работает

```
Покупатель            Шоп (этот репо)                  Кабинет (true-help)
   |                        |                                     |
   |-- /account ------------|                                     |
   |   /account/start ------|                                     |
   |                        |-- GET /login?s=домен&state&challenge (та же вкладка) |
   |<================ email → капча → код из письма =============>|
   |                        |-- GET /account/callback?token&state  |
   |                        |-- POST /api/v1/auth/token ---------->|
   |                        |   {code, secret, code_verifier}      |
   |                        |<------ access + refresh -------------|
   |   /account/orders (Alpine)                                   |
   |   /account/api/* ------|--- /api/v1/* Bearer + X-Fingerprint->|
```

- Секретный ключ хранится **только на сервере** (`shop_keys.cabinet_secret`),
  в браузер не попадает. Обмен кода — всегда сервер→сервер.
- Токены живут только в сессии сервера. В браузер уходит лишь fingerprint
   устройства (считается через `/f.js` кабинета, глобал `DeviceLib`) — он нужен,
  т.к. токен кабинета привязан к fingerprint (`X-FP` → прокси → `X-Fingerprint`).
- На 401 прокси один раз обновляет пару через `/auth/refresh` (ротация)
  и повторяет запрос; refresh протух — разлогин на `/account`.

## Сквозное шифрование (E2EE)

Тела запросов/ответов кабинета закрываются шифром: ECDH X25519 + HKDF-SHA512
+ XSalsa20-Poly1305. Шоп ретранслирует вслепую (видит Bearer/endpoint/размер,
не тела). Протокол — customerportal `docs/api-integration.md §5.4`.

- Ключ сообщения `K = HKDF(Z || K_code)`: `Z` — ECDH эфемерного ключа вкладки
  и долгоживущего ключа портала, `K_code` выведен из кода из письма. Каждое
  сообщение тем самым привязано к церемонии входа; сессии без кода идут
  на чистом ECDH (`kb=0`). Знание кода без Z для чтения недостаточно.
- `public/js/cabinet/nacl.min.js` — вендор tweetnacl 1.0.3 (закоммичен,
  обновлять только релизами с npm).
- `public/js/cabinet/e2ee.js` — наш транспорт: `ensureKeys` (ключи через
  `/account/api/e2ee/keys`, только память вкладки), `setCode` (K_code из кода
  при submit — позже кода уже нигде нет; localStorage, чистится `forgetAll`),
  `sealRequest` (POST: внутрь кладётся метод+путь — переклейка не откроется),
  `sealGet`/`openResponse` (GET: шифруется ответ), ретрай при `e2ee_rekey`.
  Нет ключей — прозрачный plaintext.
- `account.js` / `shop-cabinet.js` (`api()` в обоих) — единственные места
  вызова: POST шлют конверт с `X-E2EE: 1` + `X-E2EE-Kb`, GET шлют `X-E2EE-Epk`,
  ответы-конверты расшифровывают. Ошибки сервера — открытым текстом
  с машинным кодом (иначе не прочитать `e2ee_rekey`).
- Формы входа (`embedded.js`, `shop-login.js`) при submit кода зовут
  `E2EE.setCode(code, email)`; выход везде — `E2EE.forgetAll()`.
- `AccountController::proxy()` — форвардит `X-E2EE*` и тела как есть
  (allowlist, без чтения и логирования), `apiE2eeKeys()` — публичный релей
  ключей, `CabinetService::e2eeKeys()` — без авторизации.
- Церемония входа (email/код/капча) идёт открыто: код одноразовый,
  5 попыток, привязка контекста на стороне кабинета. Закрыть её шифром
  нельзя в принципе — шоп видит код из формы; альтернатива — внешний вход.
- Проверка интеропа JS↔PHP: `scripts/e2ee-smoke.cjs` (одним процессом:
  seal → PHP open → PHP seal → open; требует `TEST_PORTAL_PK/TEST_SEED`).

## Режимы входа: внешний и встроенный

Шоп кеширует настройки интеграции из кабинета (`CabinetService::shopSettings()`,
Cache 1 минуту, `cabinet.shop_settings`) и ветвится по флагу `embedded_login`:

- **Флаг выкл (по умолчанию)** — как раньше: `/account` сразу редиректит
  на страницы кабинета в той же вкладке (промежуточного экрана нет;
  он показывается только с текстом ошибки, иначе редирект бы зациклился),
  возвращается на `/account/callback` с одноразовым кодом.
- **Флаг вкл (менеджер разрешил в `/admin/api-clients` → «Встроенный логин в шопе»)** —
  `/account` показывает форму **у себя** в стилях страницы логина кабинета 1:1
  (зелёный лейаут, лого, hero, телефоны, OTP-ячейки): email + пазл-капча **кабинета**
  (тянется по API, своей капчи у формы нет) → шоп просит кабинет отправить код
  (`embedded/start` с одноразовым `captcha_token`), покупатель вводит код из письма
  у нас же (`embedded/verify`) → пара токенов в сессию сервера. Внешних сайтов нет вообще.
  Код из письма обязателен всегда. Повторная отправка кода — через шаг email
  заново (нужен свежий пазл).
- Формы `embedded`/`embedded-code` везут скрытое поле `fingerprint` (считает
  `device-lib.js` через тот же `hash.v4`): шоп пробрасывает его в
  `embedded/start`+`embedded/verify`, кабинет сверяет окружение запроса
  и ввода кода. Несовпадение — только запись `login_code_context_mismatch`
  в аудите кабинета, вход не блокируется.

Кеш сбрасывается при Save в `/admin/cabinet` и освежается кнопкой Test connection
(при недоступности кабинета отдаётся последнее закешированное, иначе «выкл» —
безопасный дефолт). Статус флага виден прямо на странице настроек.

## Подключение (владелец шопа + менеджер)

1. Владелец открывает `/admin/cabinet`, копирует **два значения** и отправляет менеджеру:
   - Shop domain (например `shop-example.com`);
   - Callback URL (например `https://shop-example.com/account/callback`).
2. Менеджер в админке кабинета (`/admin/api-clients`) создаёт клиента:
   domain, `callback_url`, скоупы `me, orders, orders.shipping,
   orders.tracking, orders.stop_recurring, bonus, referral, sessions,
   sessions.revoke, contact`, `orders.aff` — и выдаёт **секрет**.
3. Владелец вставляет секрет в `/admin/cabinet`, жмёт **Test connection**:
   - «Secret is valid» — можно включать;
   - «does not accept this secret» — неверный ключ, домен не зарегистрирован
     или клиент выключен (`active`) в кабинете;
   - «Cabinet is outdated» — связь и ключ в порядке, но кабинет не обновлён:
     задеплойте ветку с `shop/settings` и флагом `embedded_login`, иначе
     встроенный вход недоступен;
   - «No connection» — проверить Cabinet URL / сеть.
4. Переключатель **Enabled** + Save. Выкл — кнопка снова ведёт на true-help.

## Файлы

- `app/Services/CabinetService.php` — конфиг (`shop_keys`: `cabinet_enabled`,
  `cabinet_secret`, `cabinet_url`), PKCE, обмен/refresh/прокси-вызовы,
  кеш настроек интеграции, проверка связи.
- **Shop domain нигде не настраивается**: всегда текущий хост запроса
  (`$request->getHost()`, без www — `CabinetService::shopDomain()`).
  Все серверные вызовы (`settings`, обмен, `?s=`) используют именно его,
  иначе кабинет не узнает шоп в allowlist. Легаси-строка `cabinet_domain`
  в `shop_keys` при сохранении удаляется автоматически.
- `app/Http/Controllers/AccountController.php` — `/account/*`: хаб (ветвление
  по `embedded_login`), start/callback (внешний вход), `embeddedStart`/
  `embeddedCode`/`embeddedVerify` (встроенный вход, throttle 10/мин + своя
  капча `captcha_check`), страницы, logout, JSON-прокси.
- `app/Http/Controllers/AdminController.php` — `cabinet()`,
  `save_cabinet_info()`, `test_cabinet_connection()` (+ пункт меню
  и `pageAdminTitle`, роуты в `routes/web.php`).
- `resources/views/account/` — `layout`, `start`, `orders` (Alpine).
- `resources/views/admin/cabinet.blade.php` + компоненты `resources/views/admin/cabinet/`
  (`status`, `form`, `guide`), стили `public/admin_style/css/cabinet.css`
  и JS `public/admin_style/js/cabinet.js` (отдельные файлы только этой страницы).
- `public/js/cabinet-alpine.min.js` (вендор Alpine 3, в репо), `public/js/cabinet/account.js`,
  `public/css/cabinet-account.css` (стили 1:1 с кабинетом, скоуп `.cab-account`),
  `public/fonts/Poppins-*.woff2` (скопированы из кабинета).
- Тесты: `tests/Unit/CabinetServiceTest.php`.

## Состав кабинета

- **My orders** (`/account/orders`): список + курсорная пагинация, shipping,
  tracking, остановка автопродления.
- **Bonus** (`/account/bonus`): карта, баланс, сгорающие бонусы, история с пагинацией.
- **Referral** (`/account/referral`): ссылка + копирование, QR, купон, приглашённые заказы.
- **Sessions** (`/account/sessions`): активные сессии + «выйти везде» (отзывает и локальные токены).
- **Contact us** (`/account/contact`): форма (с Bearer капча не нужна).
- Шапка кабинета: email + баланс (из `/api/v1/me`), навигация по разделам.

## Нюансы и ограничения

- Тексты кабинета переведены только на английский (`lang/en/text.php`,
  ключи `cabinet_*`); остальные 21 язык покажут ключи — нужен проход переводами.
- Тексты кабинета переведены только на английский (`lang/en/text.php`,
  ключи `cabinet_*`); остальные 21 язык покажут ключи — нужен проход переводами.
- Лимит обмена кода в кабинете — 10/мин с IP **сервера шопа** (все покупатели
  делят лимит). Для v1 принято; при росте — вынести `shop_id` в ключ лимита
  на стороне кабинета.
- `/login` при включённом кабинете редиректит в `/account` (одна развилка в
  `HomeController@login`), ссылки в 15 дизайнах менять не пришлось.
  `design_7/8` без кнопки — поведение без изменений (404).
