<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductPackaging;
use App\Services\CabinetService;
use App\Services\CheckoutFormPrefill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Embedded customer cabinet: login only inside own shop
 * (email + cabinet puzzle captcha + emailed code; trusted networks
 * get trusted_ticket from cabinet — then no code needed).
 *
 * Tokens live in server session only.
 */
class AccountController extends Controller
{
    /**
     * Hub: tokens present — live cabinet of current design, none — login screen.
     */
    public function index()
    {
        if (!CabinetService::enabled()) {
            return redirect()->route('home.login');
        }

        if (session('cabinet.access_token')) {
            return redirect()->route('account.page', ['page' => CabinetService::firstVisiblePage()]);
        }

        return redirect()->route('account.login', ['step' => 'email']);
    }

    /**
     * Cabinet puzzle captcha for embedded login (proxy, no cookies).
     */
    public function captchaImage()
    {
        if (!CabinetService::enabled()) {
            return response()->json(['error' => 'Not available.'], 403);
        }

        $result = CabinetService::captchaGenerate();
        if (empty($result['ok'])) {
            return response()->json(['error' => 'Failed to load captcha.'], 502);
        }

        return response()->json($result['data']);
    }

    /**
     * Puzzle solution check. Success — one-time token into server session
     * (never sent to browser), later signs embedded/start.
     */
    public function captchaVerify(Request $request)
    {
        if (!CabinetService::enabled()) {
            return response()->json(['success' => false, 'reason' => 'Not available.']);
        }

        $result = CabinetService::captchaVerify((array) $request->input());
        if (empty($result['ok'])) {
            return response()->json(['success' => false, 'reason' => (string) ($result['reason'] ?? 'verify_failed')]);
        }

        session(['cabinet.captcha_token' => (string) $result['captcha_token']]);

        return response()->json(['success' => true]);
    }

    /**
     * Embedded login, step 1: email + solved cabinet puzzle → cabinet sends code.
     */
    public function embeddedStart(Request $request)
    {
        if (!CabinetService::enabled()) {
            return redirect()->route('home.login');
        }
        if (session('cabinet.access_token')) {
            return redirect()->route('account.page', ['page' => CabinetService::firstVisiblePage()]);
        }

        $email = trim((string) $request->input('email', ''));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('account.login', ['step' => 'email'])
                ->with('cabinet_embedded_error', __('text.errors_wrong_email'))
                ->withInput(['email' => $email]);
        }

        // One-time puzzle token from session (set by captchaVerify above),
        // consumed on any exit from this step.
        $captchaToken = (string) session('cabinet.captcha_token', '');
        if ($captchaToken === '') {
            return redirect()->route('account.login', ['step' => 'email'])
                ->with('cabinet_embedded_error', __('text.cabinet_captcha_required'))
                ->withInput(['email' => $email]);
        }

        $result = CabinetService::embeddedStart($email, $request->ip(), $captchaToken, $this->browserFingerprint($request));
        session()->forget('cabinet.captcha_token');

        if (!empty($result['ok'])) {
            $data = (array) ($result['data'] ?? []);
            // Trusted network: the cabinet sent trusted_ticket instead of a code —
            // exchange it for a token pair right away, skipping the code step.
            if (!empty($data['trusted']) && !empty($data['trusted_ticket'])) {
                $verify = CabinetService::embeddedVerify($email, '', $request->ip(), $this->browserFingerprint($request), (string) $data['trusted_ticket']);
                if (!empty($verify['ok'])) {
                    $this->storePair((array) $verify['data']);
                    session(['cabinet.fp_hash' => sha1((string) $this->browserFingerprint($request))]);

                    return redirect()->route('account.page', ['page' => CabinetService::firstVisiblePage()]);
                }
                $message = $this->embeddedErrorMessage((string) ($verify['code'] ?? ''), (string) ($verify['error'] ?? ''), null);

                return redirect()->route('account.login', ['step' => 'email'])
                    ->with('cabinet_embedded_error', $message)
                    ->withInput(['email' => $email]);
            }
        }
        if (empty($result['ok'])) {
            $message = $this->embeddedErrorMessage((string) ($result['code'] ?? ''), (string) ($result['error'] ?? ''), isset($result['retry_after']) ? (int) $result['retry_after'] : null);

            return redirect()->route('account.login', ['step' => 'email'])
                ->with('cabinet_embedded_error', $message)
                ->withInput(['email' => $email]);
        }

        session(['cabinet.embedded_email' => $email]);

        return redirect()->route('account.login', ['step' => 'code']);
    }

    /**
     * Embedded login, step 2: code check → pair into session.
     */
    public function embeddedVerify(Request $request)
    {
        if (!CabinetService::enabled()) {
            return redirect()->route('home.login');
        }
        if (session('cabinet.access_token')) {
            return redirect()->route('account.page', ['page' => CabinetService::firstVisiblePage()]);
        }

        $email = (string) session('cabinet.embedded_email', '');
        if ($email === '') {
            return redirect()->route('account.index');
        }

        $code = (string) preg_replace('/\D/', '', (string) $request->input('code', ''));
        if (strlen($code) !== 6) {
            return redirect()->route('account.login', ['step' => 'code'])
                ->with('cabinet_embedded_error', __('text.errors_wrong_code'));
        }

        $verifyFp = $this->browserFingerprint($request);
        $result = CabinetService::embeddedVerify($email, $code, $request->ip(), $verifyFp);
        if (empty($result['ok'])) {            $resultCode = (string) ($result['code'] ?? '');
            if ($resultCode === 'too_many_attempts') {
                session()->forget('cabinet.embedded_email');

                return redirect()->route('account.login', ['step' => 'email'])
                    ->with('cabinet_embedded_error', __('text.errors_too_many_attempts'));
            }

            return redirect()->route('account.login', ['step' => 'code'])
                ->with('cabinet_embedded_error', __('text.errors_wrong_code'));
        }

        $this->storePair((array) $result['data']);
        // Hash of the FP the cabinet bound the token to: compare the FP
        // from /account/api/* against it on 401 (raw value never stored/logged).
        session(['cabinet.fp_hash' => sha1((string) $verifyFp)]);
        session()->forget('cabinet.embedded_email');

        return redirect()->route('account.page', ['page' => CabinetService::firstVisiblePage()]);
    }

    /**
     * Autologin via emailed link (portal ?login_key= parity): exchanges
     * {login_token, fingerprint} for a token pair, no email/code steps.
     * JSON endpoint for the login page box: success carries redirectUrl,
     * failure carries a display message (manual email login stays below).
     */
    public function autologin(Request $request)
    {
        if (!CabinetService::enabled()) {
            return response()->json(['status' => 'error', 'message' => __('text.errors_login_failed')], 403);
        }
        if (session('cabinet.access_token')) {
            return response()->json([
                'status' => 'success',
                'redirectUrl' => route('account.page', ['page' => CabinetService::firstVisiblePage()]),
            ]);
        }

        $loginToken = trim((string) $request->input('login_token', ''));
        if (!CabinetService::validLoginToken($loginToken)) {
            return response()->json(['status' => 'error', 'message' => __('text.cabinet_link_invalid')], 422);
        }

        $fingerprint = $this->browserFingerprint($request);
        $result = CabinetService::redeemLoginLink($loginToken, $request->ip(), $fingerprint);
        if (empty($result['ok'])) {
            $code = (string) ($result['code'] ?? '');
            $message = match ($code) {
                'invalid_login_token', 'login_link_revoked' => __('text.cabinet_link_invalid'),
                'fingerprint_required' => __('text.cabinet_captcha_required'),
                'connection_failed' => __('text.errors_connection_failed'),
                'rate_limited' => __('text.errors_too_many_attempts'),
                default => ($result['error'] ?? '') !== '' ? (string) $result['error'] : __('text.errors_login_failed'),
            };

            return response()->json(['status' => 'error', 'message' => $message], 401);
        }

        $this->storePair((array) $result['data']);
        session(['cabinet.fp_hash' => sha1((string) $fingerprint)]);

        return response()->json([
            'status' => 'success',
            'redirectUrl' => route('account.page', ['page' => CabinetService::firstVisiblePage()]),
        ]);
    }

    /**
     * Browser fingerprint from the forms' hidden field (computed by DeviceLib):
     * the shop forwards it to the cabinet for code binding (hijack detection).
     * Empty is allowed, then the fingerprint check is skipped.
     */
    private function browserFingerprint(Request $request): ?string
    {
        $fp = trim((string) $request->input('fingerprint', ''));

        return $fp !== '' ? substr($fp, 0, 128) : null;
    }

    private function embeddedErrorMessage(string $code, string $fallback, ?int $retryAfter): string
    {
        return match ($code) {
            'unknown_email'         => __('text.errors_unknown_email'),
            'invalid_email'         => __('text.errors_wrong_email'),
            'cooldown'              => __('text.errors_code_cooldown', ['seconds' => (int) $retryAfter]),
            'rate_limited'          => __('text.errors_too_many_attempts'),
            'embedded_disabled'     => __('text.errors_embedded_disabled'),
            'unknown_shop'          => __('text.errors_login_failed'),
            'domain_required'       => __('text.errors_login_failed'),
            'captcha_required',
            'fingerprint_required'  => __('text.cabinet_captcha_required'),
            'connection_failed'     => __('text.errors_connection_failed'),
            default                 => $fallback !== '' ? $fallback : __('text.errors_login_failed'),
        };
    }

    private function storePair(array $pair): void
    {
        // The cabinet sends expires_in (seconds, ~900), no legacy expires_at.
        $expiresAt = (string) ($pair['expires_at'] ?? '');
        if ($expiresAt === '' && isset($pair['expires_in'])) {
            $expiresAt = (string) (time() + max(0, (int) $pair['expires_in']));
        }
        session([
            'cabinet.access_token'  => (string) ($pair['access_token'] ?? ''),
            'cabinet.refresh_token' => (string) ($pair['refresh_token'] ?? ''),
            'cabinet.expires_at'    => $expiresAt,
        ]);
        // Fresh login — reset scope bans and the capabilities cache.
        session()->forget('cabinet.forbidden');
        CabinetService::forgetCapabilities();
    }

    private function forgetTokens(): void
    {
        session()->forget(['cabinet.access_token', 'cabinet.refresh_token', 'cabinet.expires_at', 'cabinet.email', 'cabinet.forbidden', 'cabinet.fp_hash']);
        CabinetService::forgetCapabilities();
    }

    public function logout(Request $request)
    {
        $access = (string) session('cabinet.access_token', '');
        if ($access !== '') {
            $fp = trim((string) $request->header('X-FP', ''));
            CabinetService::logoutRemote($access, $fp !== '' ? $fp : null);
        }
        $this->forgetTokens();

        return redirect()->route('home.index');
    }

    // ---------- JSON proxy to /api/v1/* ----------

    public function apiMe(Request $request)
    {
        return $this->proxy($request, 'GET', 'me');
    }

    /**
     * Allow-list of order status filters (portal parity: no refunded here, pill only).
     * Pure function, no DB/network.
     */
    public static function allowedOrderStatus(string $status): ?string
    {
        return in_array($status, ['checking', 'approved', 'delivered', 'declined'], true) ? $status : null;
    }

    public function apiOrders(Request $request)
    {
        $query = [];
        if ($request->query('cursor') !== null && $request->query('cursor') !== '') {
            $query['cursor'] = (int) $request->query('cursor');
        }
        if ($request->query('per_page') !== null && $request->query('per_page') !== '') {
            $query['per_page'] = min(50, max(1, (int) $request->query('per_page')));
        }
        // 1:1 with the portal (Orders.vue): filter by status group.
        // No refunded in the filter (like customerportal — pill only).
        $status = self::allowedOrderStatus((string) $request->query('status', ''));
        if ($status !== null) {
            $query['status'] = $status;
        }
        // Cabinet shipping inline (with_shipping=1|true, include_shipping alias):
        // forwarded as is so the frontend can fetch addresses in one request.
        foreach (['with_shipping', 'include_shipping'] as $shipKey) {
            $shipVal = (string) $request->query($shipKey, '');
            if (in_array($shipVal, ['1', 'true'], true)) {
                $query[$shipKey] = $shipVal;
            }
        }

        $response = $this->proxy($request, 'GET', 'orders', ['query' => $query]);
        if ($response instanceof \Illuminate\Http\JsonResponse || (is_object($response) && method_exists($response, 'getStatusCode') && $response->getStatusCode() !== 200)) {
            return $response;
        }
        try {
            $body = json_decode((string) $response->getContent(), true);
        } catch (\Throwable $e) {
            return $response;
        }
        if (!is_array($body)) {
            return $response;
        }

        return response()->json(self::rewriteOrderShopLinks($body), 200);
    }

    /**
     * Rewrite order shop links through shop_domain_override (admin setting).
     * Empty override keeps links as is. Pass $override explicitly in tests;
     * null reads the display config (DB with graceful defaults).
     */
    public static function rewriteOrderShopLinks(array $body, ?string $override = null): array
    {
        if ($override === null) {
            $override = (string) (CabinetService::displayConfig()['shop_domain_override'] ?? '');
        }
        if ($override === '') {
            return $body;
        }
        $items = $body['data']['items'] ?? null;
        if (!is_array($items)) {
            return $body;
        }
        foreach ($items as $i => $order) {
            if (is_array($order) && isset($order['shop']) && is_string($order['shop']) && $order['shop'] !== '') {
                $body['data']['items'][$i]['shop'] = CabinetService::applyDomainOverride($order['shop'], $override);
            }
        }

        return $body;
    }

    /**
     * Public cabinet display config for the frontend (no PII, no token needed):
     * page/block visibility, Shop button mode, final billing URL.
     */
    public function apiDisplayConfig()
    {
        if (!CabinetService::enabled()) {
            return response()->json(['error' => 'Cabinet is disabled.'], 403);
        }
        $display = CabinetService::displayConfig();

        return response()->json([
            'pages' => $display['pages'],
            'orders_filters' => $display['orders_filters'],
            'orders_help' => $display['orders_help'],
            'orders_shop_button' => $display['orders_shop_button'],
            'billing_button' => $display['billing_button'],
            'billing_url' => CabinetService::resolveBillingUrl(),
            'billing_label' => __('text.cabinet_billing'),
        ]);
    }

    public function apiOrderShipping(Request $request, int $id)
    {
        return $this->proxy($request, 'GET', 'orders/'.$id.'/shipping');
    }

    public function apiOrderTracking(Request $request, int $id)
    {
        return $this->proxy($request, 'GET', 'orders/'.$id.'/tracking');
    }

    public function apiOrderStopRecurring(Request $request, int $id)
    {
        return $this->proxy($request, 'POST', 'orders/'.$id.'/stop-recurring');
    }

    /**
     * Fetch the API-formed repeat-checkout link for an order (best effort).
     * Null on any failure or when the API returns no link — the caller
     * falls back to the local virtual cart.
     */
    private static function cabinetRepeatUrl(string $access, string $fingerprint, int $id): ?string
    {
        try {
            $response = CabinetService::apiCall('GET', 'orders/'.$id.'/repeat-context', $access, $fingerprint);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Cabinet repeat-context failed: '.$e->getMessage());

            return null;
        }
        if (!$response->successful()) {
            \Illuminate\Support\Facades\Log::info('Cabinet repeat-context order '.$id.': http '.$response->status());

            return null;
        }
        $data = $response->json('data');
        $order = is_array($data) && isset($data['order']) && is_array($data['order']) ? $data['order'] : null;
        if (!is_array($order)) {
            \Illuminate\Support\Facades\Log::info('Cabinet repeat-context order '.$id.': no order object');

            return null;
        }
        if (!array_key_exists('repeatUrl', $order)) {
            \Illuminate\Support\Facades\Log::info('Cabinet repeat-context order '.$id.': no repeatUrl field (api binary predates the feature)');

            return null;
        }
        $url = $order['repeatUrl'];
        if (!is_string($url) || $url === '') {
            \Illuminate\Support\Facades\Log::info('Cabinet repeat-context order '.$id.': repeatUrl null/empty (nothing priced to repeat)');

            return null;
        }

        return $url;
    }

    /**
     * Apply the shop checkout-base override to an API-formed repeat link.
     * The override is the base: its host (and params like lang) win, the API
     * link fills in missing keys. Funnel identity (aff/saff) always stays
     * with the API-resolved link. Pass $override explicitly in tests; null
     * reads the display config (DB with graceful defaults).
     */
    public static function applyCheckoutOverride(string $url, ?string $override = null): string
    {
        if ($override === null) {
            $override = (string) (CabinetService::displayConfig()['checkout_base_override'] ?? '');
        }
        if ($override === '') {
            return $url;
        }

        return CabinetService::applyCheckoutBase($url, $override);
    }

    /**
     * Repeat an order: positions with packagingId from the order go to the shop
     * cart and the checkout URL is returned. Used only when the cabinet API
     * has no ready repeat link (see cabinetRepeatUrl above).
     */
    public function apiOrderRepeatVirtual(Request $request, int $id)
    {
        if (!CabinetService::enabled()) {
            return response()->json(['status' => 'error', 'message' => 'Cabinet is disabled.'], 403);
        }
        $access = (string) session('cabinet.access_token', '');
        if ($access === '') {
            return response()->json(['status' => 'error', 'message' => 'Not logged in.', 'login' => false], 401);
        }

        // Preferred path: ready repeat-checkout link formed by the cabinet API
        // (legacy true-checkout format, overridable base). Falls through to
        // the local virtual cart when the API has no link (legacy orders).
        $apiUrl = self::cabinetRepeatUrl($access, self::requestFingerprint($request), $id);
        if ($apiUrl !== null) {
            $apiUrl = self::applyCheckoutOverride($apiUrl);
            Log::info('Cabinet repeat order '.$id.': api link used');

            return response()->json(['status' => 'success', 'checkoutUrl' => $apiUrl]);
        }
        Log::info('Cabinet repeat order '.$id.': api link missing, local cart fallback');

        $items = $request->input('items', []);
        if (!is_array($items) || $items === []) {
            return response()->json(['status' => 'error', 'message' => __('text.cabinet_repeat_failed')], 422);
        }

        $added = 0;
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $packId = (int) ($item['packagingId'] ?? $item['packaging_id'] ?? 0);
            $qty = (int) ($item['quantity'] ?? 0);
            if ($packId <= 0 || $qty <= 0) {
                continue;
            }
            $pack = ProductPackaging::query()
                ->where('id', $packId)
                ->where('is_showed', 1)
                ->first();
            if ($pack === null) {
                continue;
            }
            for ($i = 0; $i < $qty; $i++) {
                Cart::add($packId);
            }
            $added++;
        }

        if ($added === 0) {
            Log::info('Cabinet repeat order '.$id.': local cart empty, nothing matched');

            return response()->json(['status' => 'error', 'message' => __('text.cabinet_repeat_failed')], 422);
        }

        Cart::update_cart_total();

        // Prefill the checkout form with the source order address right away
        // (session('form.*') keys, best effort — the cart is already built).
        $this->prefillCheckoutForm($request, $id);

        return response()->json(['status' => 'success', 'checkoutUrl' => route('checkout.index')]);
    }

    /**
     * Cabinet order shipping address → session('form.*') for checkout.
     * Best effort: no scope/network — skip silently, repeat already works.
     */
    private function prefillCheckoutForm(Request $request, int $id): void
    {
        try {
            $access = (string) session('cabinet.access_token', '');
            if ($access === '') {
                return;
            }
            $fingerprint = trim((string) $request->header('X-FP', ''));
            $response = CabinetService::apiCall('GET', 'orders/'.$id.'/shipping', $access, $fingerprint);
            if (!$response->successful()) {
                return;
            }
            $data = $response->json();
            $shipping = (is_array($data) && is_array($data['data'] ?? null)) ? $data['data'] : [];
            if (isset($shipping['shipping']) && is_array($shipping['shipping'])) {
                $shipping = $shipping['shipping'];
            }
            $prefill = CheckoutFormPrefill::fromShipping($shipping, (string) session('cabinet.email', ''));
            if ($prefill === []) {
                return;
            }
            $form = session('form', []);
            if (!is_array($form)) {
                $form = [];
            }
            session(['form' => array_merge($form, $prefill)]);
        } catch (\Throwable $e) {
            Log::warning('Cabinet repeat prefill failed: '.$e->getMessage());
        }
    }

    public function apiBonus(Request $request)
    {
        $query = [];
        if ($request->query('page') !== null && $request->query('page') !== '') {
            $query['page'] = max(1, (int) $request->query('page'));
        }
        if ($request->query('per_page') !== null && $request->query('per_page') !== '') {
            $perPage = (int) $request->query('per_page');
            $query['per_page'] = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;
        }

        return $this->proxy($request, 'GET', 'bonus/program', ['query' => $query]);
    }

    public function apiReferral(Request $request)
    {
        return $this->proxy($request, 'GET', 'referral');
    }

    public function apiSessions(Request $request)
    {
        return $this->proxy($request, 'GET', 'sessions');
    }

    public function apiSessionsRevokeAll(Request $request)
    {
        $response = $this->proxy($request, 'POST', 'sessions/revoke-all');
        // All sessions revoked — local tokens are dead too.
        if ($response->getStatusCode() === 200) {
            $this->forgetTokens();
        }

        return $response;
    }

    public function apiCapabilities(Request $request)
    {
        return $this->proxy($request, 'GET', 'account/capabilities');
    }

    public function apiContact(Request $request)
    {
        $body = [
            'name'    => trim((string) $request->input('name', '')),
            'email'   => trim((string) $request->input('email', '')),
            'subject' => trim((string) $request->input('subject', '')),
            'message' => trim((string) $request->input('message', '')),
        ];

        return $this->proxy($request, 'POST', 'contact', ['body' => $body]);
    }

    public function apiChat(Request $request)
    {
        $query = [];
        if ($request->query('offset') !== null && $request->query('offset') !== '') {
            $query['offset'] = max(0, (int) $request->query('offset'));
        }
        if ($request->query('per_page') !== null && $request->query('per_page') !== '') {
            $query['per_page'] = min(50, max(1, (int) $request->query('per_page')));
        }

        return $this->proxy($request, 'GET', 'chat/messages', ['query' => $query]);
    }

    public function apiChatSend(Request $request)
    {
        $body = [
            'subject' => trim((string) $request->input('subject', '')),
            'message' => trim((string) $request->input('message', '')),
        ];

        return $this->proxy($request, 'POST', 'chat/messages', ['body' => $body]);
    }

    /**
     * Browser fingerprint for cabinet calls: X-FP header first, ?_fp=
     * query fallback (some prod stacks strip custom headers).
     */
    private static function requestFingerprint(Request $request): string
    {
        $fingerprint = trim((string) $request->header('X-FP', ''));
        if ($fingerprint === '') {
            $fingerprint = trim((string) $request->query('_fp', ''));
        }

        return $fingerprint;
    }

    /**
     * Proxy with the server-session Bearer + browser X-FP forwarded
     * as X-Fingerprint (the cabinet token is bound to it).
     * On 401 — a single pair rotation and retry.
     */
    private function proxy(Request $request, string $method, string $path, array $options = [])
    {
        if (!CabinetService::enabled()) {
            return response()->json(['error' => 'Cabinet is disabled.'], 403);
        }

        $access = (string) session('cabinet.access_token', '');
        if ($access === '') {
            return response()->json(['error' => 'Not logged in.'], 401);
        }

        $fingerprint = self::requestFingerprint($request);
        $fpSource = trim((string) $request->header('X-FP', '')) !== '' ? 'header'
            : ($fingerprint !== '' ? 'query' : 'none');

        $response = CabinetService::apiCall($method, $path, $access, $fingerprint, $options);

        if ($response->status() === 401) {
            try {
                $cabBody = $response->json();
                $fpMatch = hash_equals((string) session('cabinet.fp_hash', ''), sha1($fingerprint));
                $fpDebug = substr(trim((string) $request->query('_fpd', '')), 0, 120);
                if ($fpDebug === '' && $fingerprint === '') {
                    // New frontend always sends _fpd with an empty FP: missing —
                    // the browser runs the old account.js (HTML/JS cache).
                    $fpDebug = 'no-fpd-param(stale-js)';
                }
                Log::warning('Cabinet proxy 401: path='.$path.' code='.(is_array($cabBody) ? (string) ($cabBody['code'] ?? $cabBody['error'] ?? 'n/a') : 'n/a').' fp_len='.strlen($fingerprint).' fp_source='.$fpSource.' fp_match='.($fpMatch ? 'yes' : 'no').($fpDebug !== '' ? ' fp_debug='.$fpDebug : ''));
            } catch (\Throwable $e) {
            }
            $refresh = (string) session('cabinet.refresh_token', '');
            if ($refresh !== '') {
                $renewed = CabinetService::refreshPair($refresh, $fingerprint !== '' ? $fingerprint : null);
                if (!empty($renewed['ok'])) {
                    $this->storePair((array) $renewed['data']);
                    $access   = (string) session('cabinet.access_token', '');
                    $response = CabinetService::apiCall($method, $path, $access, $fingerprint, $options);
                }
            }
            if ($response->status() === 401) {
                // Parallel-request race (orders+me+topbar at once):
                // the refresh token is single-use, one request wins and the rest
                // see 401. Before wiping the session check whether a sibling
                // request already stored a fresh pair, and retry with it.
                $currentAccess = (string) session('cabinet.access_token', '');
                if ($currentAccess !== '' && $currentAccess !== $access) {
                    $access = $currentAccess;
                    $response = CabinetService::apiCall($method, $path, $access, $fingerprint, $options);
                }
            }
            if ($response->status() === 401) {
                $this->forgetTokens();

                return response()->json(['error' => 'Session expired, please log in again.', 'login' => false], 401);
            }
        }

        // Scope disabled on the API client (cabinet 403) — remember it
        // to hide the matching sections in navigation.
        if ($response->status() === 403 && ($scope = CabinetService::scopeForPath($path, $method)) !== null) {
            session(['cabinet.forbidden.'.$scope => true]);
        }

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type', 'application/json'));
    }
}
