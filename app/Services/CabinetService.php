<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Embedded customer cabinet: server-side integration with the cabinet (true-help) API.
 *
 * Embedded login only: email + puzzle captcha + code from email
 * (POST {cabinet}/api/v1/auth/embedded/*, no secret on the wire —
 * the cabinet knows the shop by domain). Bootstrap — GET /api/v1/public-config.
 * Tokens live only in the server session. X-Fingerprint for /api/v1/*
 * and refresh is forwarded from the buyer browser, since the cabinet token
 * is bound to the device fingerprint (the cabinet rejects an empty
 * fingerprint with 401 fingerprint_required).
 */
class CabinetService
{
    public const KEY_ENABLED = 'cabinet_enabled';
    public const KEY_SECRET  = 'cabinet_secret';

    /**
     * Legacy shop_keys.cabinet_url key: the URL is no longer stored in the DB
     * (env CABINET_URL only), the row is deleted on save.
     */
    public const KEY_URL     = 'cabinet_url';

    public const DEFAULT_URL = 'https://api.true-help.com';

    public const SESSION_PREFIX = 'cabinet.';

    public const SETTINGS_CACHE_KEY = 'cabinet.shop_settings';
    public const SETTINGS_CACHE_TTL = 60;

    public const CAPABILITIES_CACHE_KEY = 'cabinet.capabilities';
    public const CAPABILITIES_CACHE_TTL = 60;

    /**
     * Config: enabled/secret from shop_keys, URL from env CABINET_URL only
     * (DEFAULT_URL fallback). Safe defaults when rows are missing.
     * The shop domain is not stored here: it always comes from the current
     * request host (see shopDomain()/autoHost()).
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
     * Cabinet URL: env CABINET_URL only, otherwise DEFAULT_URL.
     * Not configurable via DB/admin panel.
     */
    public static function cabinetUrl(): string
    {
        $env = trim((string) env('CABINET_URL', ''));

        return $env !== '' ? rtrim($env, '/') : self::DEFAULT_URL;
    }

    /**
     * Save settings (insert when rows are missing).
     * URL is not saved here (env CABINET_URL only).
     * Legacy cabinet_url/cabinet_domain rows (manual overrides) are deleted
     * on save: URL comes from env, domain from the request host.
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
     * Shop domain for the cabinet (all server-side calls).
     * Always the current request host ($request->getHost(), no www):
     * it is never set manually in admin, otherwise the cabinet won't
     * recognize the shop in the allowlist and embedded login breaks.
     */
    public static function shopDomain(): string
    {
        return self::autoHost();
    }

    /**
     * Current request host ($request->getHost(), no www, lowercased) —
     * the only source of the shop domain for the cabinet.
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
     * Pair rotation via refresh_token. The cabinet requires a fingerprint
     * (body or X-Fingerprint), without it answers 401 fingerprint_required.
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
     * Logical cabinet scope for an /api/v1/* path (1:1 with the cabinet
     * ApiClientPolicy). Lets the shop remember a 403 "scope disabled" and hide
     * matching sections. null — path outside scopes.
     * Chat differs by method: read — chat, send — chat.send.
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
        if (preg_match('~^orders/\d+/(shipping|tracking|stop-recurring|repeat-virtual|repeat-context)$~', $path, $m)) {
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
     * Direct /api/v1/* call on behalf of the buyer.
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
     * Revoke the current pair on the cabinet side (best effort).
     * The cabinet Bearer middleware also requires X-Fingerprint — send it
     * when known, otherwise logout silently fails (401).
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
     * Integration settings from the cabinet (1 minute cache): shop_id,
     * scopes/chat_enabled for navigation (60s cache, like capabilities).
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

        // Cabinet unreachable — serve the last cached copy, otherwise defaults
        // (safe: embedded login stays off).
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
     * Available scopes of the current buyer (60s cache per shop domain).
     * Returns null without a token/connection; otherwise an array shaped
     * ['scopes' => string[], 'shop_id' => string, 'chat_enabled' => bool].
     * Cache is 1 minute, like shopSettings, to avoid hitting the cabinet per request.
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

        // Fast path: scopes already in shopSettings (no Bearer, no fingerprint)
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

        // No network in tests — no token with a real fingerprint there
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
                    // No server-side fingerprint: the cabinet answers 401
                    // fingerprint_required — fine, the caller falls back
                    // to scopes from public-config (see capabilities()).
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
     * Shop bootstrap from the cabinet: GET /api/v1/public-config?domain=.
     * Needs no secret (none on the wire), always answers wrapped in {data: {...}}.
     *
     * @return array{shop_id: string, embedded_login: bool, scopes?: string[]|null, chat_enabled?: bool}|null null — no connection/shop not registered
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
     * Cabinet puzzle captcha for embedded login (no cookies: the server keeps
     * a mirror of the puzzle in shared cache, see CaptchaService).
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
     * Puzzle solution check. Success yields a one-time captcha_token
     * (kept in the server session, never sent to the browser).
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
     * Embedded login, step 1: ask the cabinet to send a code to the email.
     * Needs a one-time captcha_token (cabinet puzzle solved above).
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
     * Embedded login, step 2: check the code (or a trusted ticket from
     * a trusted network — then no code is needed), return a token pair.
     * The cabinet answers {token_type, access_token, refresh_token, expires_in}.
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
     * Autologin link shape check (portal ?login_key= parity): lkl_ prefix,
     * bounded length. Crypto verification happens server-side on redeem.
     */
    public static function validLoginToken(string $token): bool
    {
        $token = trim($token);
        if ($token === '' || strlen($token) > 2048) {
            return false;
        }

        return str_starts_with($token, 'lkl_');
    }

    /**
     * Autologin, single step: exchange an emailed login link for a token
     * pair (fingerprint-bound, like embedded verify). The cabinet answers
     * {token_type, access_token, refresh_token, expires_in}.
     *
     * @return array{ok: bool, data?: array, code?: string, error?: string}
     */
    public static function redeemLoginLink(string $loginToken, ?string $customerIp, ?string $fingerprint = null): array
    {
        $cfg = self::config();

        $payload = [
            'domain' => self::shopDomain(),
            'login_token' => $loginToken,
            'fingerprint' => $fingerprint,
            'customer_ip' => $customerIp,
        ];

        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->connectTimeout(5)
                ->post($cfg['url'].'/api/v1/auth/login-link/redeem', $payload);
        } catch (\Throwable $e) {
            Log::error('CabinetService login-link redeem failed: '.$e->getMessage());

            return ['ok' => false, 'code' => 'connection_failed', 'error' => 'No connection to cabinet.'];
        }

        $body = $response->json();
        $body = is_array($body) ? $body : [];
        if ($response->successful() && !empty($body['access_token'])) {
            return ['ok' => true, 'data' => $body];
        }

        return [
            'ok' => false,
            'code' => (string) ($body['code'] ?? 'request_failed'),
            'error' => (string) ($body['error'] ?? 'Cabinet request failed.'),
        ];
    }
    /**
     * Browser flow with no secret on the wire: the cabinet knows the shop
     * by domain (or shop_id), the shop_keys secret is never sent.
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
     * Connection check: GET /api/v1/public-config?domain=. No secret on the wire
     * (the $secret parameter stays for compatibility and is ignored).
     * Cabinet URL comes from env CABINET_URL only (see cabinetUrl()),
     * the shop domain always comes from the current request host
     * ($request->getHost()) — no need to pass them manually.
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
     * Pure interpretation of the cabinet response for the connection check (network-free testable).
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

    // ---------- Display settings (portal parity: Order view) ----------

    /**
     * shop_keys key for display settings (single JSON for the whole cabinet).
     */
    public const DISPLAY_KEY = 'cabinet_display';

    public const SHOP_BUTTON_ALL = 'all';
    public const SHOP_BUTTON_FOREIGN = 'foreign';
    public const SHOP_BUTTON_HIDE = 'hide';

    /**
     * Defaults: everything visible, Shop button for any order with a link (portal parity).
     */
    public static function displayDefaults(): array
    {
        return [
            'pages' => ['orders' => true, 'bonus' => true, 'referral' => true, 'contact' => true, 'chat' => true],
            'orders_filters' => true,
            'orders_help' => true,
            'orders_shop_button' => self::SHOP_BUTTON_ALL,
            'shop_domain_override' => '',
            'billing_url_override' => '',
            'checkout_base_override' => '',
            'billing_button' => true,
        ];
    }

    /**
     * Pure normalization of raw input (admin form, DB): allowlist + types.
     * Unknown keys are dropped, malformed URLs are reset.
     */
    public static function normalizeDisplayConfig(array $raw): array
    {
        $out = self::displayDefaults();

        if (isset($raw['pages']) && is_array($raw['pages'])) {
            foreach (array_keys($out['pages']) as $page) {
                if (array_key_exists($page, $raw['pages'])) {
                    $out['pages'][$page] = self::toBool($raw['pages'][$page]);
                }
            }
        }

        foreach (['orders_filters', 'orders_help', 'billing_button'] as $flag) {
            if (array_key_exists($flag, $raw)) {
                $out[$flag] = self::toBool($raw[$flag]);
            }
        }

        if (array_key_exists('orders_shop_button', $raw)) {
            $mode = strtolower(trim((string) $raw['orders_shop_button']));
            if (in_array($mode, [self::SHOP_BUTTON_ALL, self::SHOP_BUTTON_FOREIGN, self::SHOP_BUTTON_HIDE], true)) {
                $out['orders_shop_button'] = $mode;
            }
        }

        foreach (['shop_domain_override', 'billing_url_override', 'checkout_base_override'] as $urlKey) {
            if (array_key_exists($urlKey, $raw)) {
                $out[$urlKey] = self::sanitizeOverrideUrl((string) $raw[$urlKey]);
            }
        }

        return $out;
    }

    private static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return $value != 0;
        }
        $value = strtolower(trim((string) $value));

        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }

    /**
     * Override must be an http(s) URL with a host, otherwise reset.
     */
    private static function sanitizeOverrideUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }
        if (!preg_match('~^https?://~i', $url)) {
            $url = 'https://'.$url;
        }
        $parts = parse_url($url);
        if (!is_array($parts) || empty($parts['host'])) {
            return '';
        }
        if (!preg_match('~^[A-Za-z0-9.-]+$~', (string) $parts['host'])) {
            return '';
        }

        return $url;
    }

    /**
     * First cabinet page allowed by display settings (post-login landing).
     */
    public static function firstVisiblePage(): string
    {
        $pages = (array) (self::displayConfig()['pages'] ?? []);
        foreach (['orders', 'bonus', 'referral', 'contact', 'chat'] as $page) {
            if (!empty($pages[$page])) {
                return $page;
            }
        }

        return 'orders';
    }

    /**
     * Read display config (shop_keys DB, no network). Without DB — defaults.
     */
    public static function displayConfig(): array
    {
        $defaults = self::displayDefaults();

        try {
            $raw = DB::table('shop_keys')->where('name_key', '=', self::DISPLAY_KEY)->value('key_data');
        } catch (\Throwable $e) {
            Log::error('CabinetService display config read failed: '.$e->getMessage());

            return $defaults;
        }

        if (!is_string($raw) || $raw === '') {
            return $defaults;
        }
        $decoded = json_decode($raw, true);

        return self::normalizeDisplayConfig(is_array($decoded) ? $decoded : []);
    }

    /**
     * Save display config (validated upsert). Stores only values that differ
     * from defaults: the legacy shop_keys.key_data column is a short VARCHAR
     * and a full dump would not fit (1406 Data too long).
     */
    public static function saveDisplay(array $data): void
    {
        DB::table('shop_keys')->updateOrInsert(
            ['name_key' => self::DISPLAY_KEY],
            ['key_data' => json_encode(self::displayDiff(self::normalizeDisplayConfig($data)), JSON_UNESCAPED_SLASHES)]
        );
    }

    /**
     * Strip defaults from a normalized config for compact storage.
     * Pure function (no DB).
     */
    public static function displayDiff(array $normalized): array
    {
        $defaults = self::displayDefaults();
        $diff = [];
        foreach ($normalized as $key => $value) {
            if ($key === 'pages' && is_array($value)) {
                $pageDiff = [];
                foreach ($value as $page => $visible) {
                    if ($visible !== ($defaults['pages'][$page] ?? null)) {
                        $pageDiff[$page] = $visible;
                    }
                }
                if ($pageDiff !== []) {
                    $diff['pages'] = $pageDiff;
                }
            } elseif ($value !== ($defaults[$key] ?? null)) {
                $diff[$key] = $value;
            }
        }

        return $diff;
    }

    /**
     * Final billing URL: shop override, otherwise cabinet billing_base_url.
     */
    public static function resolveBillingUrl(): string
    {
        $override = (string) (self::displayConfig()['billing_url_override'] ?? '');
        if ($override !== '') {
            return $override;
        }
        $settings = self::shopSettings();

        return trim((string) ($settings['billing_base_url'] ?? ''));
    }

    /**
     * Apply a checkout-base override to an API-formed link (repeat order).
     * Opposite precedence to applyDomainOverride: the OVERRIDE is the base
     * (host, scheme, port, path and its params win), the link fills in keys
     * the override lacks (products, auth, ...). Funnel identity (aff/saff)
     * always stays with the API-resolved link and is never overridden.
     * Example: link .../order/begin?products=P&lang=en&aff=9&auth=T
     *   + override https://true-checkout.com?lang=es
     *   => https://true-checkout.com/order/begin?products=P&lang=es&aff=9&auth=T
     * Pure function (no network/DB). On parse errors the link is unchanged.
     */
    public static function applyCheckoutBase(string $link, string $override): string
    {
        $link = trim($link);
        $override = trim($override);
        if ($link === '' || $override === '') {
            return $link;
        }

        if (!preg_match('~^https?://~i', $override)) {
            $override = 'https://'.$override;
        }
        $ov = parse_url($override);
        if (!is_array($ov) || empty($ov['host'])) {
            return $link;
        }

        $isAbsolute = (bool) preg_match('~^https?://~i', $link);
        $lp = $isAbsolute ? parse_url($link) : parse_url('https://placeholder.invalid/'.ltrim($link, '/'));
        if (!is_array($lp)) {
            return $link;
        }

        $scheme = strtolower((string) ($ov['scheme'] ?? 'https'));
        if (!in_array($scheme, ['http', 'https'], true)) {
            $scheme = 'https';
        }
        $host = strtolower((string) $ov['host']);
        $port = isset($ov['port']) ? ':'.((int) $ov['port']) : '';

        $ovPath = (string) ($ov['path'] ?? '');
        if ($ovPath !== '' && $ovPath !== '/') {
            $path = $ovPath;
        } else {
            $path = $isAbsolute ? (string) ($lp['path'] ?? '') : '/'.ltrim((string) ($lp['path'] ?? ''), '/');
            if ($path !== '' && $path[0] !== '/') {
                $path = '/'.$path;
            }
        }

        $ovQuery = [];
        if (isset($ov['query']) && $ov['query'] !== '') {
            parse_str((string) $ov['query'], $ovQuery);
        }
        $linkQuery = [];
        if (isset($lp['query']) && $lp['query'] !== '') {
            parse_str((string) $lp['query'], $linkQuery);
        }
        foreach ($linkQuery as $k => $v) {
            if (!array_key_exists($k, $ovQuery)) {
                $ovQuery[$k] = $v;
            }
        }
        // Funnel identity is API-resolved and never overridden.
        foreach (['aff', 'saff'] as $protected) {
            if (array_key_exists($protected, $linkQuery)) {
                $ovQuery[$protected] = $linkQuery[$protected];
            }
        }
        $query = $ovQuery !== [] ? '?'.http_build_query($ovQuery) : '';

        $fragment = '';
        if (isset($ov['fragment']) && $ov['fragment'] !== '') {
            $fragment = '#'.$ov['fragment'];
        } elseif (isset($lp['fragment']) && $lp['fragment'] !== '') {
            $fragment = '#'.$lp['fragment'];
        }

        return $scheme.'://'.$host.$port.$path.$query.$fragment;
    }

    /**
     * Apply a domain override to an API link (Shop button).
     *
     * Semantics: scheme/host(/port) come from the override; path comes from
     * the override when set, otherwise from the link; query merge: link params
     * win, missing ones are appended from the override. Example:
     *   link https://shop1.example?aff=5 + override https://example.com?aff=1842
     *   => https://example.com?aff=5
     *   link https://shop1.example/catalog + override https://example.com?aff=1842
     *   => https://example.com/catalog?aff=1842
     * Pure function (no network/DB). On parse errors the link is unchanged.
     */
    public static function applyDomainOverride(string $link, string $override): string
    {
        $link = trim($link);
        $override = trim($override);
        if ($link === '' || $override === '') {
            return $link;
        }

        if (!preg_match('~^https?://~i', $override)) {
            $override = 'https://'.$override;
        }
        $ov = parse_url($override);
        if (!is_array($ov) || empty($ov['host'])) {
            return $link;
        }

        $isAbsolute = (bool) preg_match('~^https?://~i', $link);
        $lp = $isAbsolute ? parse_url($link) : parse_url('https://placeholder.invalid/'.ltrim($link, '/'));
        if (!is_array($lp)) {
            return $link;
        }

        $scheme = strtolower((string) ($ov['scheme'] ?? 'https'));
        if (!in_array($scheme, ['http', 'https'], true)) {
            $scheme = 'https';
        }
        $host = strtolower((string) $ov['host']);
        $port = isset($ov['port']) ? ':'.((int) $ov['port']) : '';

        $ovPath = (string) ($ov['path'] ?? '');
        if ($ovPath !== '' && $ovPath !== '/') {
            $path = $ovPath;
        } else {
            $path = $isAbsolute ? (string) ($lp['path'] ?? '') : '/'.ltrim((string) ($lp['path'] ?? ''), '/');
            if ($path !== '' && $path[0] !== '/') {
                $path = '/'.$path;
            }
        }

        $linkQuery = [];
        if (isset($lp['query']) && $lp['query'] !== '') {
            parse_str((string) $lp['query'], $linkQuery);
        }
        $ovQuery = [];
        if (isset($ov['query']) && $ov['query'] !== '') {
            parse_str((string) $ov['query'], $ovQuery);
        }
        foreach ($ovQuery as $k => $v) {
            if (!array_key_exists($k, $linkQuery)) {
                $linkQuery[$k] = $v;
            }
        }
        $query = $linkQuery !== [] ? '?'.http_build_query($linkQuery) : '';

        $fragment = isset($lp['fragment']) && $lp['fragment'] !== '' ? '#'.$lp['fragment'] : '';

        return $scheme.'://'.$host.$port.$path.$query.$fragment;
    }
}
