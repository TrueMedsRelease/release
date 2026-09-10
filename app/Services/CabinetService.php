<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Встроенный личный кабинет: серверная интеграция с кабинетом (true-help) по API.
 *
 * Схема: секретный ключ хранится только на сервере шопа (shop_keys).
 * Обмен одноразового кода на пару токенов идёт сервер→сервер
 * (POST {cabinet}/api/v1/auth/token {code, secret, code_verifier}),
 * токены лежат только в сессии сервера. X-Fingerprint для /api/v1/*
 * пробрасывается из браузера покупателя (считается через /fingerprint.js
 * кабинета), т.к. токен кабинета привязан к fingerprint устройства.
 */
class CabinetService
{
    public const KEY_ENABLED = 'cabinet_enabled';
    public const KEY_SECRET  = 'cabinet_secret';
    public const KEY_URL     = 'cabinet_url';
    public const KEY_DOMAIN  = 'cabinet_domain';

    public const DEFAULT_URL = 'https://true-help.com';

    public const SESSION_PREFIX = 'cabinet.';

    public const SETTINGS_CACHE_KEY = 'cabinet.shop_settings';
    public const SETTINGS_CACHE_TTL = 600;

    /**
     * Конфиг из shop_keys. Безопасные дефолты при отсутствии строк.
     */
    public static function config(): array
    {
        $enabled = false;
        $secret  = '';
        $url     = self::DEFAULT_URL;
        $domain  = '';

        try {
            $rows = DB::table('shop_keys')
                ->whereIn('name_key', [self::KEY_ENABLED, self::KEY_SECRET, self::KEY_URL, self::KEY_DOMAIN])
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
            if (isset($rows[self::KEY_DOMAIN])) {
                $domain = mb_strtolower(trim((string) $rows[self::KEY_DOMAIN]));
            }
        } catch (\Throwable $e) {
            Log::error('CabinetService config read failed: '.$e->getMessage());
        }

        return [
            'enabled' => $enabled,
            'secret'  => $secret,
            'url'     => rtrim($url, '/'),
            'domain'  => $domain,
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
     */
    public static function save(array $data): void
    {
        $map = [
            self::KEY_ENABLED => isset($data['enabled']) && $data['enabled'] ? '1' : '0',
            self::KEY_SECRET  => trim((string) ($data['secret'] ?? '')),
            self::KEY_URL     => rtrim(trim((string) ($data['url'] ?? self::DEFAULT_URL)) ?: self::DEFAULT_URL, '/'),
            self::KEY_DOMAIN  => mb_strtolower(trim((string) ($data['domain'] ?? ''))),
        ];

        foreach ($map as $nameKey => $keyData) {
            DB::table('shop_keys')->updateOrInsert(
                ['name_key' => $nameKey],
                ['key_data' => $keyData]
            );
        }
    }

    /**
     * Публичный callback шопа — его владелец передаёт менеджеру
     * вместе с доменом для allowlist кабинета.
     */
    public static function callbackUrl(): string
    {
        return rtrim(request()->getSchemeAndHttpHost(), '/').'/account/callback';
    }

    /**
     * Домен шопа для кабинета (?s= и все серверные вызовы).
     * Явно заданный в настройках — иначе текущий хост. Оверрайд нужен,
     * когда шоп открывают не с боевого домена (локалка, зеркала).
     */
    public static function shopDomain(): string
    {
        $configured = self::config()['domain'];
        if ($configured !== '') {
            return (string) preg_replace('~^www\.~i', '', $configured);
        }

        return self::autoHost();
    }

    /**
     * Текущий хост запроса (без оверрайда) — для подсказок в админке.
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
     * PKCE-пара (RFC 7636) для привязки старта логина к обмену.
     *
     * @return array{verifier: string, challenge: string, method: string}
     */
    public static function pkcePair(): array
    {
        $verifier  = self::randomBase64Url(64);
        $digest    = hash('sha256', $verifier, true);
        $challenge = rtrim(strtr(base64_encode($digest), '+/', '-_'), '=');

        return ['verifier' => $verifier, 'challenge' => $challenge, 'method' => 'S256'];
    }

    public static function randomBase64Url(int $bytes): string
    {
        return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
    }

    /**
     * Ссылка старта логина в кабинете (handoff ?s= + PKCE).
     */
    public static function loginUrl(string $cabinetUrl, string $domain, string $state, string $challenge, string $method = 'S256'): string
    {
        $q = http_build_query([
            's'                     => $domain,
            'state'                 => $state,
            'code_challenge'        => $challenge,
            'code_challenge_method' => $method,
        ]);

        return rtrim($cabinetUrl, '/').'/login?'.$q;
    }

    /**
     * Серверный обмен кода на пару токенов (secret-флоу).
     *
     * @return array{ok: bool, data?: array, error?: string}
     */
    public static function exchangeCode(string $code, string $verifier): array
    {
        $cfg = self::config();

        if ($cfg['secret'] === '') {
            return ['ok' => false, 'error' => 'cabinet_secret_missing'];
        }

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->connectTimeout(5)
                ->post($cfg['url'].'/api/v1/auth/token', [
                    'code'          => $code,
                    'secret'        => $cfg['secret'],
                    'code_verifier' => $verifier,
                ]);
        } catch (\Throwable $e) {
            Log::error('CabinetService exchange failed: '.$e->getMessage());

            return ['ok' => false, 'error' => 'connection_failed'];
        }

        $body = $response->json();
        if (!$response->successful() || empty($body['access_token'])) {
            return ['ok' => false, 'error' => (string) ($body['error'] ?? 'exchange_failed')];
        }

        return ['ok' => true, 'data' => $body];
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
     * Прямой вызов /api/v1/* от имени покупателя.
     */
    public static function apiCall(string $method, string $path, string $accessToken, string $fingerprint, array $options = [])
    {
        $cfg = self::config();
        $url = $cfg['url'].'/api/v1/'.ltrim($path, '/');

        $pending = Http::acceptJson()
            ->timeout(15)
            ->connectTimeout(5)
            ->withHeaders([
                'Authorization'  => 'Bearer '.$accessToken,
                'X-Fingerprint' => $fingerprint,
            ]);

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
     * Настройки интеграции из кабинета (кеш 10 минут): шоп ветвится по ним —
     * встроенный логин у себя либо уход на внешние страницы кабинета,
     * а внутри встроенного — нужен ли код из письма (skip_2fa).
     *
     * @return array{shop_id: string, embedded_login: bool, skip_2fa: bool}
     */
    public static function shopSettings(bool $force = false): array
    {
        $defaults = ['shop_id' => '', 'embedded_login' => false, 'skip_2fa' => false];

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
     * @return array{shop_id: string, embedded_login: bool, skip_2fa: bool}|null null — нет секрета/связи
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

        return [
            'shop_id'        => (string) ($body['shop_id'] ?? ''),
            'embedded_login' => (bool) $body['embedded_login'],
            'skip_2fa'       => (bool) ($body['skip_2fa'] ?? false),
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
    public static function embeddedStart(string $email, ?string $customerIp, ?string $captchaToken): array
    {
        return self::embeddedCall('auth/embedded/start', [
            'email'         => $email,
            'customer_ip'   => $customerIp,
            'captcha_token' => $captchaToken,
        ]);
    }

    /**
     * Встроенный логин, шаг 2: проверить код, вернуть пару токенов.
     *
     * @return array{ok: bool, data?: array, code?: string, error?: string}
     */
    public static function embeddedVerify(string $email, string $code, ?string $customerIp): array
    {
        return self::embeddedCall('auth/embedded/verify', [
            'email'       => $email,
            'code'        => $code,
            'customer_ip' => $customerIp,
        ]);
    }

    /**
     * Встроенный логин без кода из письма (только при skip_2fa у клиента):
     * email + пазл → сразу пара токенов.
     *
     * @return array{ok: bool, data?: array, code?: string, error?: string}
     */
    public static function embeddedDirect(string $email, ?string $customerIp, ?string $captchaToken): array
    {
        return self::embeddedCall('auth/embedded/direct', [
            'email'         => $email,
            'customer_ip'   => $customerIp,
            'captcha_token' => $captchaToken,
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
     * Проверка связи.
     * Шаг 1: новый эндпоинт настроек (с доменом) — заодно узнаём флаг
     * embedded-входа. Шаг 2 (фолбэк для старого кабинета без settings):
     * обмен заведомо мусорным кодом — секрет верный + домен зарегистрирован,
     * если кабинет отвечает "Invalid or expired code.".
     *
     * @return array{status: string, text: string, embedded: bool|null}
     *   status: ok|not_registered|outdated|fail
     */
    public static function testConnection(string $secret, string $url, string $domain): array
    {
        $url = rtrim(trim($url) ?: self::DEFAULT_URL, '/');
        $domain = mb_strtolower(trim($domain));

        if ($secret === '' || $domain === '') {
            return ['status' => 'fail', 'text' => 'Enter the secret key and the shop domain first.', 'embedded' => null];
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
                'text' => 'Secret is valid, shop is registered. Embedded login: '.($embedded ? 'allowed.' : 'not allowed, external cabinet pages will be used.'),
                'embedded' => $embedded,
            ];
        }

        $body = $response->json();
        $body = is_array($body) ? $body : [];
        $error = (string) ($body['error'] ?? '');

        // Старого эндпоинта настроек нет — кабинет не обновлён.
        // Проверяем хотя бы связку домен+секрет обменом с мусорным кодом.
        if ($response->status() === 404 && $error === '') {
            $legacy = self::legacyProbe($url, $domain, $secret);
            if ($legacy['status'] === 'ok') {
                $legacy['status'] = 'outdated';
                $legacy['text'] .= ' Cabinet is outdated: update it to use embedded login and settings.';
            }

            return $legacy;
        }

        $result = self::interpretTestConnection($response->status(), $body);
        $result['embedded'] = null;

        return $result;
    }

    /**
     * Проба для старого кабинета (без /api/v1/shop/settings):
     * обмен заведомо мусорным кодом с доменом.
     *
     * @return array{status: string, text: string, embedded: null}
     */
    private static function legacyProbe(string $url, string $domain, string $secret): array
    {
        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->connectTimeout(5)
                ->post($url.'/api/v1/auth/token', [
                    'domain' => $domain,
                    'secret' => $secret,
                    'code'   => 'connection-probe-0000000000000000',
                ]);
        } catch (\Throwable $e) {
            return ['status' => 'fail', 'text' => 'No connection to cabinet: '.$e->getMessage(), 'embedded' => null];
        }

        $result = self::interpretTestConnection($response->status(), (array) $response->json());
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

        if ($error === 'Invalid or expired code.') {
            return ['status' => 'ok', 'text' => 'Secret is valid, shop is registered. You can enable the cabinet.'];
        }

        if ($error === 'Unknown shop or bad secret.') {
            return ['status' => 'not_registered', 'text' => 'Cabinet does not accept this secret. Check the key and ask the manager to register the shop domain.'];
        }

        if ($httpStatus === 429) {
            return ['status' => 'fail', 'text' => 'Too many attempts, try again in a minute.'];
        }

        return ['status' => 'fail', 'text' => $error !== '' ? $error : 'Unexpected cabinet response (HTTP '.$httpStatus.').'];
    }
}
