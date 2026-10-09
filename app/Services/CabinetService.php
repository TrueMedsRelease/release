<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Встроенный личный кабинет: серверная интеграция с кабинетом (true-help) по API.
 *
 * Вход только встроенный: email + пазл-капча + код из письма
 * (POST {cabinet}/api/v1/auth/embedded/*, секрета на проводе нет —
 * кабинет знает шоп по domain). Bootstrap — GET /api/v1/public-config.
 * Токены лежат только в сессии сервера. X-Fingerprint для /api/v1/*
 * и refresh пробрасывается из браузера покупателя, т.к. токен кабинета
 * привязан к fingerprint устройства (пустой fingerprint кабинет отвергает
 * 401 fingerprint_required).
 */
class CabinetService
{
    public const KEY_ENABLED = 'cabinet_enabled';
    public const KEY_SECRET  = 'cabinet_secret';

    /**
     * Легаси-ключ shop_keys.cabinet_url: URL больше не хранится в БД
     * (только env CABINET_URL), строка при сохранении удаляется.
     */
    public const KEY_URL     = 'cabinet_url';

    public const DEFAULT_URL = 'https://api.true-help.com';

    public const SESSION_PREFIX = 'cabinet.';

    public const SETTINGS_CACHE_KEY = 'cabinet.shop_settings';
    public const SETTINGS_CACHE_TTL = 60;

    public const CAPABILITIES_CACHE_KEY = 'cabinet.capabilities';
    public const CAPABILITIES_CACHE_TTL = 60;

    /**
     * Конфиг: enabled/secret из shop_keys, URL только из env CABINET_URL
     * (дефолт DEFAULT_URL). Безопасные дефолты при отсутствии строк.
     * Домен шопа здесь не хранится: он всегда берётся из текущего
     * хоста запроса (см. shopDomain()/autoHost()).
     */
    public static function config(): array
    {
        $enabled = false;
        $secret  = '';

        try {
            $rows = DB::table('shop_keys')
                ->whereIn('name_key', [self::KEY_ENABLED, self::KEY_SECRET])
                ->pluck('key_data', 'name_key')
                ->toArray();

            if (isset($rows[self::KEY_ENABLED])) {
                $enabled = ((string) $rows[self::KEY_ENABLED] === '1');
            }
            if (isset($rows[self::KEY_SECRET])) {
                $secret = trim((string) $rows[self::KEY_SECRET]);
            }
        } catch (\Throwable $e) {
            Log::error('CabinetService config read failed: '.$e->getMessage());
        }

        return [
            'enabled' => $enabled,
            'secret'  => $secret,
            'url'     => self::cabinetUrl(),
            'domain'  => self::autoHost(),
        ];
    }

    public static function enabled(): bool
    {
        return self::config()['enabled'];
    }

    public static function secret(): string
    {
        return self::config()['secret'];
    }

    /**
     * URL кабинета: только env CABINET_URL, иначе DEFAULT_URL.
     * В БД/админке не настраивается.
     */
    public static function cabinetUrl(): string
    {
        $env = trim((string) env('CABINET_URL', ''));

        return $env !== '' ? rtrim($env, '/') : self::DEFAULT_URL;
    }

    /**
     * Сохранение настроек (insert при отсутствии строк).
     * URL здесь не сохраняется (только env CABINET_URL).
     * Легаси-строки cabinet_url/cabinet_domain (ручные оверрайды) при
     * сохранении удаляются: URL — из env, домен — из хоста запроса.
     */
    public static function save(array $data): void
    {
        $map = [
            self::KEY_ENABLED => isset($data['enabled']) && $data['enabled'] ? '1' : '0',
            self::KEY_SECRET  => trim((string) ($data['secret'] ?? '')),
        ];

        foreach ($map as $nameKey => $keyData) {
            DB::table('shop_keys')->updateOrInsert(
                ['name_key' => $nameKey],
                ['key_data' => $keyData]
            );
        }

        try {
            DB::table('shop_keys')->whereIn('name_key', [self::KEY_URL, 'cabinet_domain'])->delete();
        } catch (\Throwable $e) {
            Log::error('CabinetService legacy cleanup failed: '.$e->getMessage());
        }
    }

    /**
     * Домен шопа для кабинета (все серверные вызовы).
     * Всегда текущий хост запроса ($request->getHost(), без www):
     * руками в админке он не задаётся, иначе кабинет не узнает шоп
     * в allowlist и встроенный вход сломается.
     */
    public static function shopDomain(): string
    {
        return self::autoHost();
    }

    /**
     * Текущий хост запроса ($request->getHost(), без www, lowercased) —
     * единственный источник домена шопа для кабинета.
     */
    public static function autoHost(): string
    {
        try {
            $host = (string) request()->getHost();
        } catch (\Throwable $e) {
            return '';
        }

        return (string) preg_replace('~^www\.~i', '', mb_strtolower(trim($host)));
    }

    /**
     * Ротация пары по refresh_token. Кабинет требует fingerprint
     * (тело или X-Fingerprint), без него отвечает 401 fingerprint_required.
     *
     * @return array{ok: bool, data?: array, error?: string}
     */
    public static function refreshPair(string $refreshToken, ?string $fingerprint = null): array
    {
        $cfg = self::config();
        $fp = $fingerprint !== null ? trim($fingerprint) : '';

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->connectTimeout(5)
                ->withHeaders(['X-Fingerprint' => $fp])
                ->post($cfg['url'].'/api/v1/auth/refresh', [
                    'refresh_token' => $refreshToken,
                    'fingerprint'   => $fp,
                ]);
        } catch (\Throwable $e) {
            Log::error('CabinetService refresh failed: '.$e->getMessage());

            return ['ok' => false, 'error' => 'connection_failed'];
        }

        $body = $response->json();
        if (!$response->successful() || empty($body['access_token'])) {
            return ['ok' => false, 'error' => (string) ($body['error'] ?? 'refresh_failed')];
        }

        return ['ok' => true, 'data' => $body];
    }

    /**
     * Логический скоуп кабинета для пути /api/v1/* (1:1 с ApiClientPolicy
     * кабинета). Нужен, чтобы шоп запоминал 403 «скоуп выключен» и прятал
     * соответствующие разделы. null — путь вне скоупов.
     * Чат различаем по методу: чтение — chat, отправка — chat.send.
     */
    public static function scopeForPath(string $path, string $method = 'GET'): ?string
    {
        $path = trim($path, '/');
        if ($path === 'me') {
            return 'me';
        }
        if ($path === 'orders') {
            return 'orders';
        }
        if (preg_match('~^orders/\d+/(shipping|tracking|stop-recurring|repeat-virtual)$~', $path, $m)) {
            return match ($m[1]) {
                'shipping'       => 'orders.shipping',
                'tracking'       => 'orders.tracking',
                'stop-recurring' => 'orders.stop_recurring',
                default          => 'orders',
            };
        }
        if ($path === 'bonus/program') {
            return 'bonus';
        }
        if ($path === 'referral') {
            return 'referral';
        }
        if ($path === 'sessions') {
            return 'sessions';
        }
        if ($path === 'sessions/revoke-all') {
            return 'sessions.revoke';
        }
        if ($path === 'contact') {
            return 'contact';
        }
        if ($path === 'chat/messages') {
            return strtoupper($method) === 'POST' ? 'chat.send' : 'chat';
        }

        return null;
    }

    /**
     * Прямой вызов /api/v1/* от имени покупателя.
     */
    public static function apiCall(string $method, string $path, string $accessToken, string $fingerprint, array $options = [])
    {
        $cfg = self::config();
        $url = $cfg['url'].'/api/v1/'.ltrim($path, '/');

        $headers = [
            'Authorization'  => 'Bearer '.$accessToken,
            'X-Fingerprint' => $fingerprint,
        ];

        $pending = Http::acceptJson()
            ->timeout(15)
            ->connectTimeout(5)
            ->withHeaders($headers);

        $method = strtoupper($method);
        if ($method === 'GET') {
            return $pending->get($url, $options['query'] ?? []);
        }

        return $pending->post($url, $options['body'] ?? []);
    }

    /**
     * Отзыв текущей пары на стороне кабинета (best effort).
     * Bearer-мидлварь кабинета требует и X-Fingerprint — шлём его,
     * когда известен, иначе logout молча не сработает (401).
     */
    public static function logoutRemote(string $accessToken, ?string $fingerprint = null): void
    {
        $cfg = self::config();
        $headers = ['Authorization' => 'Bearer '.$accessToken];
        if ($fingerprint !== null && trim($fingerprint) !== '') {
            $headers['X-Fingerprint'] = trim($fingerprint);
        }

        try {
            Http::acceptJson()
                ->timeout(10)
                ->connectTimeout(5)
                ->withHeaders($headers)
                ->post($cfg['url'].'/api/v1/auth/logout');
        } catch (\Throwable $e) {
            Log::error('CabinetService remote logout failed: '.$e->getMessage());
        }
    }

    /**
     * Настройки интеграции из кабинета (кеш 1 минуту): shop_id,
     * scopes/chat_enabled для навигации (кеш 60с, как и capabilities).
     *
     * @return array{shop_id: string, embedded_login: bool, scopes?: string[]|null, chat_enabled?: bool}
     */
    public static function shopSettings(bool $force = false): array
    {
        $defaults = ['shop_id' => '', 'embedded_login' => false];

        try {
            $cached = Cache::get(self::SETTINGS_CACHE_KEY);
        } catch (\Throwable $e) {
            $cached = null;
        }
        if (!$force && is_array($cached) && array_key_exists('embedded_login', $cached)) {
            return $cached + $defaults;
        }

        $fresh = self::fetchShopSettings();
        if ($fresh !== null) {
            try {
                Cache::put(self::SETTINGS_CACHE_KEY, $fresh, self::SETTINGS_CACHE_TTL);
            } catch (\Throwable $e) {
                Log::error('CabinetService settings cache failed: '.$e->getMessage());
            }

            return $fresh;
        }

        // Кабинет недоступен — отдаём последнее закешированное, иначе дефолты
        // (безопасно: встроенный логин выключен).
        if (is_array($cached) && array_key_exists('embedded_login', $cached)) {
            return $cached + $defaults;
        }

        return $defaults;
    }

    public static function forgetShopSettings(): void
    {
        try {
            Cache::forget(self::SETTINGS_CACHE_KEY);
        } catch (\Throwable $e) {
            Log::error('CabinetService settings cache clear failed: '.$e->getMessage());
        }
    }

    /**
     * Доступные скоупы текущего покупателя (кеш 60с на домен шопа).
     * Возвращает null если нет токена/связи; иначе массив вида
     * ['scopes' => string[], 'shop_id' => string, 'chat_enabled' => bool].
     * Кеш — 1 минута, как и shopSettings, чтобы не дергать кабинет каждый хит.
     *
     * @return array{scopes: string[], shop_id: string, chat_enabled: bool}|null
     */
    public static function capabilities(): ?array
    {
        $access = (string) session('cabinet.access_token', '');
        if ($access === '') {
            return null;
        }
        $domain = self::shopDomain();
        $cacheKey = self::CAPABILITIES_CACHE_KEY . '.' . ($domain !== '' ? $domain : 'default');

        try {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && isset($cached['scopes']) && is_array($cached['scopes'])) {
                return $cached;
            }
        } catch (\Throwable $e) {
            $cached = null;
        }

        // Быстрый путь: scopes уже в shopSettings (без Bearer, без fingerprint)
        $settings = self::shopSettings();
        if (isset($settings['scopes']) && is_array($settings['scopes'])) {
            $fallback = [
                'scopes' => $settings['scopes'],
                'shop_id' => (string) ($settings['shop_id'] ?? ''),
                'chat_enabled' => (bool) ($settings['chat_enabled'] ?? true),
            ];
            try {
                Cache::put($cacheKey, $fallback, self::CAPABILITIES_CACHE_TTL);
            } catch (\Throwable $e) {
            }

            return $fallback;
        }

        // В тестах не лезем в сеть — нет токена с реальным fingerprint
        if (app()->environment('testing')) {
            return null;
        }

        $fresh = self::fetchCapabilities($access);
        if ($fresh !== null) {
            try {
                Cache::put($cacheKey, $fresh, self::CAPABILITIES_CACHE_TTL);
            } catch (\Throwable $e) {
                Log::error('CabinetService capabilities cache failed: '.$e->getMessage());
            }

            return $fresh;
        }

        if (is_array($cached) && isset($cached['scopes'])) {
            return $cached;
        }

        return null;
    }

    public static function forgetCapabilities(): void
    {
        $domain = self::shopDomain();
        $cacheKey = self::CAPABILITIES_CACHE_KEY . '.' . ($domain !== '' ? $domain : 'default');
        try {
            Cache::forget($cacheKey);
        } catch (\Throwable $e) {
            Log::error('CabinetService capabilities cache clear failed: '.$e->getMessage());
        }
    }

    /**
     * @return array{scopes: string[], shop_id: string, chat_enabled: bool}|null
     */
    private static function fetchCapabilities(string $accessToken): ?array
    {
        $cfg = self::config();
        if ($cfg['url'] === '') {
            return null;
        }

        try {
            $response = Http::acceptJson()
                ->timeout(10)
                ->connectTimeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    // Серверного fingerprint нет: кабинет ответит 401
                    // fingerprint_required — это нормально, вызывающий
                    // откатится на scopes из public-config (см. capabilities()).
                    'X-Fingerprint' => '',
                ])
                ->get($cfg['url'] . '/api/v1/account/capabilities');
        } catch (\Throwable $e) {
            Log::error('CabinetService capabilities fetch failed: '.$e->getMessage());

            return null;
        }

        if (!$response->successful()) {
            return null;
        }

        $body = $response->json();
        $data = is_array($body['data'] ?? null) ? $body['data'] : (is_array($body) ? $body : null);
        if (!is_array($data) || !isset($data['scopes']) || !is_array($data['scopes'])) {
            return null;
        }

        return [
            'scopes' => array_values(array_map('strval', $data['scopes'])),
            'shop_id' => (string) ($data['shop_id'] ?? ''),
            'chat_enabled' => (bool) ($data['chat_enabled'] ?? true),
        ];
    }

    /**
     * Bootstrap шопа из кабинета: GET /api/v1/public-config?domain=.
     * Секрета не требует (его на проводе нет), отвечает всегда
     * в обёртке {data: {...}}.
     *
     * @return array{shop_id: string, embedded_login: bool, scopes?: string[]|null, chat_enabled?: bool}|null null — нет связи/шоп не зарегистрирован
     */
    private static function fetchShopSettings(): ?array
    {
        $cfg = self::config();
        $domain = self::shopDomain();
        if ($domain === '') {
            return null;
        }

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->connectTimeout(5)
                ->get($cfg['url'].'/api/v1/public-config', [
                    'domain' => $domain,
                ]);
        } catch (\Throwable $e) {
            Log::error('CabinetService settings fetch failed: '.$e->getMessage());

            return null;
        }

        if (!$response->successful()) {
            return null;
        }
        $body = $response->json();
        $data = is_array($body['data'] ?? null) ? $body['data'] : null;
        if (!is_array($data) || !array_key_exists('embedded_login', $data)) {
            return null;
        }

        $scopes = isset($data['scopes']) && is_array($data['scopes'])
            ? array_values(array_map('strval', $data['scopes']))
            : null;

        return [
            'shop_id'        => (string) ($data['shop_id'] ?? ''),
            'embedded_login' => (bool) $data['embedded_login'],
            'scopes'         => $scopes,
            'chat_enabled'   => isset($data['chat_enabled']) ? (bool) $data['chat_enabled'] : true,
        ];
    }

    /**
     * Пазл-капча кабинета для встроенного логина (без кук: сервер хранит
     * зеркало пазла в общем кеше, см. CaptchaService).
     *
     * @return array{ok: bool, data?: array, error?: string}
     */
    public static function captchaGenerate(): array
    {
        $cfg = self::config();

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->connectTimeout(5)
                ->get($cfg['url'].'/api/captcha');
        } catch (\Throwable $e) {
            Log::error('CabinetService captcha generate failed: '.$e->getMessage());

            return ['ok' => false, 'error' => 'connection_failed'];
        }

        $body = $response->json();
        $body = is_array($body) ? $body : [];
        if (!$response->successful() || !isset($body['token'])) {
            return ['ok' => false, 'error' => 'captcha_failed'];
        }

        return ['ok' => true, 'data' => $body];
    }

    /**
     * Проверка решения пазла. Успех даёт одноразовый captcha_token
     * (держим в сессии сервера, в браузер не отдаём).
     *
     * @return array{ok: bool, captcha_token?: string, reason?: string}
     */
    public static function captchaVerify(array $payload): array
    {
        $cfg = self::config();

        $forward = [
            'token' => (string) ($payload['token'] ?? ''),
            'x' => (float) ($payload['x'] ?? -1),
            'y' => (float) ($payload['y'] ?? -1),
            'totalTime' => isset($payload['totalTime']) ? (int) $payload['totalTime'] : null,
        ];
        $events = $payload['mouseEvents'] ?? $payload['events'] ?? [];
        $forward['mouseEvents'] = is_array($events) ? array_slice(array_values($events), 0, 200) : [];

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->connectTimeout(5)
                ->post($cfg['url'].'/api/captcha/verify', $forward);
        } catch (\Throwable $e) {
            Log::error('CabinetService captcha verify failed: '.$e->getMessage());

            return ['ok' => false, 'reason' => 'connection_failed'];
        }

        $body = $response->json();
        $body = is_array($body) ? $body : [];
        if (empty($body['success']) || empty($body['captcha_token'])) {
            return ['ok' => false, 'reason' => (string) ($body['reason'] ?? 'verify_failed')];
        }

        return ['ok' => true, 'captcha_token' => (string) $body['captcha_token']];
    }

    /**
     * Встроенный логин, шаг 1: попросить кабинет отправить код на email.
     * Нужен одноразовый captcha_token (пазл кабинета, решённый выше).
     *
     * @return array{ok: bool, code?: string, error?: string, retry_after?: int}
     */
    public static function embeddedStart(string $email, ?string $customerIp, ?string $captchaToken, ?string $fingerprint = null): array
    {
        return self::embeddedCall('auth/embedded/start', [
            'email'         => $email,
            'customer_ip'   => $customerIp,
            'captcha_token' => $captchaToken,
            'fingerprint'   => $fingerprint,
        ]);
    }

    /**
     * Встроенный логин, шаг 2: проверить код (или trusted-тикет
     * с доверенной сети — тогда код не нужен), вернуть пару токенов.
     * Кабинет отдаёт {token_type, access_token, refresh_token, expires_in}.
     *
     * @return array{ok: bool, data?: array, code?: string, error?: string}
     */
    public static function embeddedVerify(string $email, string $code, ?string $customerIp, ?string $fingerprint = null, ?string $trustedTicket = null): array
    {
        $payload = [
            'email'       => $email,
            'code'        => $code,
            'customer_ip' => $customerIp,
            'fingerprint' => $fingerprint,
        ];
        if ($trustedTicket !== null && $trustedTicket !== '') {
            $payload['trusted_ticket'] = $trustedTicket;
        }

        return self::embeddedCall('auth/embedded/verify', $payload);
    }

    /**
     * Браузерный флоу без секрета на проводе: кабинет знает шоп
     * по domain (или shop_id), секрет shop_keys не отправляется.
     *
     * @return array{ok: bool, data?: array, code?: string, error?: string, retry_after?: int}
     */
    private static function embeddedCall(string $path, array $payload): array
    {
        $cfg = self::config();

        $payload['domain'] = self::shopDomain();
        unset($payload['secret']);

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->connectTimeout(5)
                ->post($cfg['url'].'/api/v1/'.$path, $payload);
        } catch (\Throwable $e) {
            Log::error('CabinetService embedded call failed: '.$e->getMessage());

            return ['ok' => false, 'code' => 'connection_failed', 'error' => 'No connection to cabinet.'];
        }

        $body = $response->json();
        $body = is_array($body) ? $body : [];
        if ($response->successful()) {
            return ['ok' => true, 'data' => $body];
        }

        $result = [
            'ok'    => false,
            'code'  => (string) ($body['code'] ?? 'request_failed'),
            'error' => (string) ($body['error'] ?? 'Cabinet request failed.'),
        ];
        if (isset($body['retry_after'])) {
            $result['retry_after'] = (int) $body['retry_after'];
        }

        return $result;
    }

    /**
     * Проверка связи: GET /api/v1/public-config?domain=. Секрета на проводе
     * нет (параметр $secret оставлен для совместимости и игнорируется).
     * URL кабинета — только env CABINET_URL (см. cabinetUrl()),
     * домен шопа всегда берётся из текущего хоста запроса
     * ($request->getHost()) — передавать их руками не нужно.
     *
     * @return array{status: string, text: string, embedded: bool|null}
     *   status: ok|not_registered|fail
     */
    public static function testConnection(string $secret = ''): array
    {
        $url = self::cabinetUrl();
        $domain = self::autoHost();

        if ($domain === '') {
            return ['status' => 'fail', 'text' => 'Cannot detect the shop host from the request.', 'embedded' => null];
        }

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->connectTimeout(5)
                ->get($url.'/api/v1/public-config', [
                    'domain' => $domain,
                ]);
        } catch (\Throwable $e) {
            return ['status' => 'fail', 'text' => 'No connection to cabinet: '.$e->getMessage(), 'embedded' => null];
        }

        if ($response->successful()) {
            $body = $response->json();
            $data = is_array($body['data'] ?? null) ? $body['data'] : null;
            $embedded = is_array($data) && array_key_exists('embedded_login', $data)
                ? (bool) $data['embedded_login']
                : null;

            return [
                'status' => 'ok',
                'text' => 'Shop is registered in the cabinet. You can enable the cabinet.',
                'embedded' => $embedded,
            ];
        }

        $body = $response->json();
        $body = is_array($body) ? $body : [];

        $result = self::interpretTestConnection($response->status(), $body);
        $result['embedded'] = null;

        return $result;
    }

    /**
     * Чистая интерпретация ответа кабинета для проверки связи (тестируемо без сети).
     *
     * @return array{status: string, text: string}
     */
    public static function interpretTestConnection(int $httpStatus, array $body): array
    {
        $code = (string) ($body['code'] ?? '');
        $error = (string) ($body['error'] ?? '');

        if ($code === 'unknown_shop' || $error === 'Unknown shop or bad secret.') {
            return ['status' => 'not_registered', 'text' => 'Shop domain is not registered in the cabinet. Ask the manager to register the shop domain.'];
        }

        if ($code === 'embedded_disabled' || $error === 'Cabinet login is disabled for this shop.') {
            return ['status' => 'fail', 'text' => 'Shop is registered but cabinet login is disabled for it. Ask the manager to activate the shop.'];
        }

        if ($httpStatus === 429) {
            return ['status' => 'fail', 'text' => 'Too many attempts, try again in a minute.'];
        }

        return ['status' => 'fail', 'text' => $error !== '' ? $error : 'Unexpected cabinet response (HTTP '.$httpStatus.').'];
    }
}
