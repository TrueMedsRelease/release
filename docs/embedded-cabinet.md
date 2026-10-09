# Встроенный личный кабинет (My orders внутри шопа)

Кнопка «My Account» ведёт покупателя во встроенный кабинет шопа —
вход полностью у себя: email + пазл-капча + код из письма.
Переключается в админке: **Customer Cabinet** (`/admin/cabinet`).

## Как это работает

```
Покупатель            Шоп (этот репо)                  Кабинет (true-help)
   |                        |                                     |
   |-- /account ------------|                                     |
   |   /cabinet-login/email (дизайн шопа, Alpine)                 |
   |   email + пазл-капча кабинета (GET /api/captcha)             |
|                        |-- POST /api/v1/auth/embedded/start ->|
|                        |   {domain, email, captcha_token,      |
|                        |    fingerprint, customer_ip}          |
|<================ код из письма =============================>|
|   /cabinet-login/code  |                                     |
|                        |-- POST /api/v1/auth/embedded/verify>|
|                        |   {domain, email, code, fingerprint,  |
|                        |    customer_ip}                       |
|                        |<------ access + refresh -------------|
   |   /cabinet/orders (дизайн из сессии, Alpine)                 |
   |   /account/api/* ------|--- /api/v1/* Bearer + X-Fingerprint>|
```

Кабинет живёт в дизайнах (`design_*/cabinet/*`, дизайн из сессии — в URL
его нет): `/cabinet/{orders,bonus,referral,contact,chat}`,
вход — `/cabinet-login/{email,captcha,code}`. Старые общие страницы
`account/*` удалены; `/account` остался хабом-редиректом, `/account/api/*`
и embedded-endpoint'ы — без изменений.

- Секрета на проводе нет: кабинет знает шоп по domain (bootstrap —
  `GET /api/v1/public-config?domain=`). Поле `shop_keys.cabinet_secret`
  хранится для совместимости, в API не отправляется. Обмен кода — всегда
  сервер→сервер.
- Токены живут только в сессии сервера. В браузер уходит лишь fingerprint
   устройства (считается через `/f.js` кабинета, глобал `DeviceLib`) — он
  обязателен: токен кабинета привязан к fingerprint (`X-FP` → прокси →
  `X-Fingerprint`), пустой fingerprint кабинет отвергает 401.
- На 401 прокси один раз обновляет пару через `/auth/refresh`
  (с тем же fingerprint, ротация) и повторяет запрос;
  refresh протух — разлогин на `/account`.

## Вход: только встроенный

Дизайн-страницы входа `/cabinet-login/email|captcha|code` в скине текущего
дизайна (общие partials `cabinet-parts/*`, JS `shop-login.js`): email +
пазл-капча **кабинета** (тянется по API, своей капчи у формы нет) → шоп просит
кабинет отправить код (`embedded/start` с одноразовым `captcha_token`),
покупатель вводит код из письма у нас же (`embedded/verify`) → пара токенов
в сессию сервера. Внешних сайтов нет вообще.
Код из письма обязателен почти всегда. Повторная отправка кода — через шаг email
заново (нужен свежий пазл).
- С доверенных сетей кабинет вместо `sent` отвечает
  `{trusted: true, trusted_ticket}` — шоп сразу меняет тикет на пару токенов
  (`embedded/verify` с `trusted_ticket`, без кода), шаг с кодом пропускается.
  Тикет одноразовый, привязан к email+fingerprint+IP.
- Формы `embedded`/`embedded-code` везут скрытое поле `fingerprint` (считает
  `device-lib.js` через тот же `hash.v4`): шоп пробрасывает его в
  `embedded/start`+`embedded/verify`+`refresh`. Пустой fingerprint кабинет
  отвергает (`fingerprint_required` → просим пройти пазл заново).

Кеш настроек (`cabinet.shop_settings`, 1 минута) сбрасывается при Save
в `/admin/cabinet` и освежается кнопкой Test connection
(при недоступности кабинета отдаётся последнее закешированное).

## Подключение (владелец шопа + менеджер)

1. Владелец открывает `/admin/cabinet`, копирует **домен шопа**
   (например `shop-example.com`) и отправляет менеджеру.
2. Менеджер в админке кабинета (`/admin/api-clients`) создаёт клиента:
   domain, скоупы `me, orders, orders.shipping,
   orders.tracking, orders.stop_recurring, bonus, referral, sessions,
   sessions.revoke, contact, chat, chat.send`.
3. Владелец жмёт **Test connection** (проверяет регистрацию домена
   через `public-config`, секрет не нужен):
   - ok — можно включать;
   - not_registered — домен не зарегистрирован в кабинете;
   - disabled — шоп зарегистрирован, но логин выключен (`active`) в кабинете;
   - «No connection» — проверить `CABINET_URL` в env / сеть.
   URL кабинета задаётся только через env `CABINET_URL`
   (дефолт `https://api.true-help.com`), в админке он показан read-only.
4. Переключатель **Enabled** + Save.

## Файлы

- `app/Services/CabinetService.php` — конфиг (`shop_keys`: `cabinet_enabled`,
  `cabinet_secret`; URL только env `CABINET_URL`, дефолт
  `https://api.true-help.com`), refresh/прокси-вызовы,
  кеш настроек интеграции, проверка связи.
- **Shop domain нигде не настраивается**: всегда текущий хост запроса
  (`$request->getHost()`, без www — `CabinetService::shopDomain()`).
  Все серверные вызовы (`public-config`, `embedded/*`) используют именно его,
  иначе кабинет не узнает шоп. Легаси-строки `cabinet_url`/`cabinet_domain`
  в `shop_keys` при сохранении удаляются автоматически.
- `app/Http/Controllers/AccountController.php` — `/account/*`: хаб,
  `embeddedStart`/`embeddedVerify` (встроенный вход, throttle 10/мин),
  logout, JSON-прокси.
  Старых страничных экшенов больше нет — страницы отдаёт
  `CabinetPreviewController` (`/cabinet/*`, `/cabinet-login/*`, дизайн
  из сессии).
- `app/Http/Controllers/AdminController.php` — `cabinet()`,
  `save_cabinet_info()`, `test_cabinet_connection()` (+ пункт меню
  и `pageAdminTitle`, роуты в `routes/web.php`).
- `resources/views/design_*/cabinet/` — per-design кабинет (13 дизайнов):
  `live-*` (живые данные через `/account/api/*`), `login-*` (вход);
  общие куски — `cabinet-live/*`, `cabinet-parts/*` (только login-части).
- `resources/views/admin/cabinet.blade.php` + компоненты `resources/views/admin/cabinet/`
  (`status`, `form`, `guide`), стили `public/admin_style/css/cabinet.css`
  и JS `public/admin_style/js/cabinet.js` (отдельные файлы только этой страницы).
- `public/js/cabinet-alpine.min.js` (вендор Alpine 3, в репо), `public/js/cabinet/account.js`,
  `shop-cabinet.js` (транспорт + Alpine-компоненты),
  `public/fonts/Poppins-*.woff2` (скопированы из кабинета).
- Тесты: `tests/Unit/CabinetServiceTest.php`, `tests/Feature/CabinetPreviewTest.php`.

## Состав кабинета

- **My orders** (`/cabinet/orders`): список + курсорная пагинация, shipping,
  tracking, остановка автопродления.
- **Bonus** (`/cabinet/bonus`): карта, баланс, сгорающие бонусы, история с пагинацией.
- **Referral** (`/cabinet/referral`): ссылка + копирование, QR, купон, приглашённые заказы.
- **Contact us** (`/cabinet/contact`): форма (с Bearer капча не нужна).
- **Chat** (`/cabinet/chat`): переписка с поддержкой.
- Страницы Sessions в дизайнах нет (только API `sessions`/`revoke-all` живут
  в прокси) — UI перенесём при необходимости.
- Шапка кабинета: email + баланс (из `/api/v1/me`), навигация по разделам.

## Нюансы и ограничения

- Тексты кабинета переведены только на английский (`lang/en/text.php`,
  ключи `cabinet_*`); остальные 21 язык покажут ключи — нужен проход переводами.
- Тексты кабинета переведены только на английский (`lang/en/text.php`,
  ключи `cabinet_*`); остальные 21 язык покажут ключи — нужен проход переводами.
- Лимиты встроенного входа в кабинете — 10/мин с IP + 60/мин на шопа
  (start и verify отдельно).
- `/login` при включённом кабинете редиректит в `/account` (одна развилка в
  `HomeController@login`), ссылки в 15 дизайнах менять не пришлось.
  `design_7/8` без кнопки — поведение без изменений (404).
