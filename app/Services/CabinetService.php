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
 * (POST {cabinet}/api/v1/auth/embedded/* с секретом из shop_keys).
 * Секретный ключ хранится только на сервере шопа, токены лежат
 * только в сессии сервера. X-Fingerprint для /api/v1/* пробрасывается
 * из браузера покупателя, т.к. токен кабинета привязан к fingerprint устройства.
 */
class CabinetService
{
    public const KEY_ENABLED = 'cabinet_enabled';
    public const KEY_SECRET  = 'cabinet_secret';
    public const KEY_URL     = 'cabinet_url';

    public const DEFAULT_URL = 'https://true-help.com';

    public const SESSION_PREFIX = 'cabinet.';

    public const SETTINGS_CACHE_KEY = 'cabinet.shop_settings';
    public const SETTINGS_CACHE_TTL = 60;

    public const CAPABILITIES_CACHE_KEY = 'cabinet.capabilities';
    public const CAPABILITIES_CACHE_TTL = 60;

    /**
     * Конфиг из shop_keys. Безопасные дефолты при отсутствии строк.
     * Домен шопа здесь не хранится: он всегда берётся из текущего
     * хоста запроса (см. shopDomain()/autoHost()).
     */
    public static function config(): array
    {
        $enabled = false;
        $secret  = '';
        $url     = self::DEFAULT_URL;

        try {
            $rows = DB::table('shop_keys')
                ->whereIn('name_key', [self::KEY_ENABLED, self::KEY_SECRET, self::KEY_URL])
                ->pluck('key_data', 'name_key')
                ->toArray();

            if (isset($rows[self::KEY_ENABLED])) {
                $enabled = ((string) $rows[self::KEY_ENABLED] === '1');
            }
            if (isset($rows[self::KEY_SECRET])) {
                $secret = trim((string) $rows[self::KEY_SECRET]);
            }
            if (isset($rows[self::KEY_URL]) && trim((string) $rows[self::KEY_URL]) !== '') {
                $url = trim((string) $rows[self::KEY_URL]);
            }
        } catch (\Throwable $e) {
            Log::error('CabinetService config read failed: '.$e->getMessage());
        }

        return [
            'enabled' => $enabled,
            'secret'  => $secret,
            'url'     => rtrim($url, '/'),
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

    public static function cabinetUrl(): string
    {
        return self::config()['url'];
    }

    /**
     * Сохранение настроек (insert при отсутствии строк).
     * Легаси-строка cabinet_domain (ручной оверрайд домена) при
     * сохранении удаляется: домен всегда определяется из хоста запроса.
     */
    public static function save(array $data): void
    {
        $map = [
            self::KEY_ENABLED => isset($data['enabled']) && $data['enabled'] ? '1' : '0',
            self::KEY_SECRET  => trim((string) ($data['secret'] ?? '')),
            self::KEY_URL     => rtrim(trim((string) ($data['url'] ?? self::DEFAULT_URL)) ?: self::DEFAULT_URL, '/'),
        ];

        foreach ($map as $nameKey => $keyData) {
            DB::table('shop_keys')->updateOrInsert(
                ['name_key' => $nameKey],
                ['key_data' => $keyData]
            );
        }

        try {
            DB::table('shop_keys')->where('name_key', 'cabinet_domain')->delete();
        } catch (\Throwable $e) {
            Log::error('CabinetService legacy domain cleanup failed: '.$e->getMessage());
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
     * Ротация пары по refresh_token.
     *
     * @return array{ok: bool, data?: array, error?: string}
     */
    public static function refreshPair(string $refreshToken): array
    {
        $cfg = self::config();

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->connectTimeout(5)
                ->post($cfg['url'].'/api/v1/auth/refresh', [
                    'refresh_token' => $refreshToken,
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
     */
    public static function logoutRemote(string $accessToken): void
    {
        $cfg = self::config();

        try {
            Http::acceptJson()
                ->timeout(10)
                ->connectTimeout(5)
                ->withHeaders(['Authorization' => 'Bearer '.$accessToken])
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
                    // Портал привязывает токен к fingerprint, но для
                    // серверного префетча fingerprint неизвестен — шлём
                    // пустой, портал его проигнорирует если был пустой при логине.
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
     * @return array{shop_id: string, embedded_login: bool}|null null — нет секрета/связи
     */
    private static function fetchShopSettings(): ?array
    {
        $cfg = self::config();
        if ($cfg['secret'] === '') {
            return null;
        }

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->connectTimeout(5)
                ->post($cfg['url'].'/api/v1/shop/settings', [
                    'domain' => self::shopDomain(),
                    'secret' => $cfg['secret'],
                ]);
        } catch (\Throwable $e) {
            Log::error('CabinetService settings fetch failed: '.$e->getMessage());

            return null;
        }

        if (!$response->successful()) {
            return null;
        }
        $body = $response->json();
        if (!is_array($body) || !array_key_exists('embedded_login', $body)) {
            return null;
        }

        $scopes = isset($body['scopes']) && is_array($body['scopes'])
            ? array_values(array_map('strval', $body['scopes']))
            : null;

        return [
            'shop_id'        => (string) ($body['shop_id'] ?? ''),
            'embedded_login' => (bool) $body['embedded_login'],
            'scopes'         => $scopes,
            'chat_enabled'   => isset($body['chat_enabled']) ? (bool) $body['chat_enabled'] : true,
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
     * Встроенный логин, шаг 2: проверить код, вернуть пару токенов.
     *
     * @return array{ok: bool, data?: array, code?: string, error?: string}
     */
    public static function embeddedVerify(string $email, string $code, ?string $customerIp, ?string $fingerprint = null): array
    {
        return self::embeddedCall('auth/embedded/verify', [
            'email'       => $email,
            'code'        => $code,
            'customer_ip' => $customerIp,
            'fingerprint' => $fingerprint,
        ]);
    }

    /**
     * Встроенный логин без кода из письма: email + пазл (+ разовый пропуск
     * из админки) → сразу пара токенов.
     *
     * @return array{ok: bool, data?: array, code?: string, error?: string}
     */
    public static function embeddedDirect(string $email, ?string $customerIp, ?string $captchaToken, ?string $fingerprint = null, ?string $bypassToken = null): array
    {
        return self::embeddedCall('auth/embedded/direct', [
            'email'         => $email,
            'customer_ip'   => $customerIp,
            'captcha_token' => $captchaToken,
            'fingerprint'   => $fingerprint,
            'bypass_token'  => $bypassToken,
        ]);
    }

    /**
     * @return array{ok: bool, data?: array, code?: string, error?: string, retry_after?: int}
     */
    private static function embeddedCall(string $path, array $payload): array
    {
        $cfg = self::config();
        if ($cfg['secret'] === '') {
            return ['ok' => false, 'code' => 'not_configured', 'error' => 'Cabinet secret is missing.'];
        }

        $payload['domain'] = self::shopDomain();
        $payload['secret'] = $cfg['secret'];

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
     * Проверка связи: POST /api/v1/shop/settings с доменом и секретом.
     * Домен шопа всегда берётся из текущего хоста запроса
     * ($request->getHost()) — передавать его руками не нужно.
     *
     * @return array{status: string, text: string, embedded: bool|null}
     *   status: ok|not_registered|fail
     */
    public static function testConnection(string $secret, string $url): array
    {
        $url = rtrim(trim($url) ?: self::DEFAULT_URL, '/');
        $domain = self::autoHost();

        if ($secret === '') {
            return ['status' => 'fail', 'text' => 'Enter the secret key first.', 'embedded' => null];
        }
        if ($domain === '') {
            return ['status' => 'fail', 'text' => 'Cannot detect the shop host from the request.', 'embedded' => null];
        }

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->connectTimeout(5)
                ->post($url.'/api/v1/shop/settings', [
                    'domain' => $domain,
                    'secret' => $secret,
                ]);
        } catch (\Throwable $e) {
            return ['status' => 'fail', 'text' => 'No connection to cabinet: '.$e->getMessage(), 'embedded' => null];
        }

        if ($response->successful()) {
            $body = $response->json();
            $embedded = is_array($body) && array_key_exists('embedded_login', $body)
                ? (bool) $body['embedded_login']
                : null;

            return [
                'status' => 'ok',
                'text' => 'Secret is valid, shop is registered. You can enable the cabinet.',
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
        $error = (string) ($body['error'] ?? '');

        if ($error === 'Unknown shop or bad secret.') {
            return ['status' => 'not_registered', 'text' => 'Cabinet does not accept this secret. Check the key and ask the manager to register the shop domain.'];
        }

        if ($httpStatus === 429) {
            return ['status' => 'fail', 'text' => 'Too many attempts, try again in a minute.'];
        }

        return ['status' => 'fail', 'text' => $error !== '' ? $error : 'Unexpected cabinet response (HTTP '.$httpStatus.').'];
    }
}
