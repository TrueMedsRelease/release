<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductPackaging;
use App\Services\CabinetService;
use App\Services\CheckoutFormPrefill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Встроенный личный кабинет (работает только при включённом
 * флаге в /admin/cabinet, иначе редирект на старый вход).
 *
 * Внешний вход — только прямой редирект: уход на кабинет и возврат
 * на /account/callback (та же вкладка, без попапов).
 * Обмен code→токены всегда сервер→сервер с секретом из shop_keys,
 * токены живут только в сессии сервера.
 */
class AccountController extends Controller
{
    /**
     * Хаб: есть токены — живой кабинет текущего дизайна, нет — экран входа.
     * Разовый пропуск без кода (?bypass= из админки кабинета) забираем
     * в сессию сервера — дальше он едет только сервер→сервер.
     */
    public function index(Request $request)
    {
        if (!CabinetService::enabled()) {
            return redirect()->route('home.login');
        }

        if (session('cabinet.access_token')) {
            return redirect()->route('cabinet.live.page', ['page' => 'orders']);
        }

        $bypass = trim((string) $request->query('bypass', ''));
        if ($bypass !== '') {
            session(['cabinet.bypass_token' => substr($bypass, 0, 128)]);
        }
        if (!CabinetService::enabled()) {
            return redirect()->route('home.login');
        }

        if (session('cabinet.access_token')) {
            return redirect()->route('cabinet.live.page', ['page' => 'orders']);
        }

        $settings = CabinetService::shopSettings();
        if ($settings['embedded_login']) {
            return redirect()->route('cabinet.login', ['step' => 'email']);
        }

        if (session('cabinet_error')) {
            return redirect()->route('cabinet.login', ['step' => 'email'])
                ->with('cabinet_embedded_error', (string) session('cabinet_error', ''));
        }

        return $this->start();
    }

    /**
     * Пазл-капча кабинета для встроенного логина (прокси, без кук).
     */
    public function captchaImage()
    {
        if (!CabinetService::enabled() || !CabinetService::shopSettings()['embedded_login']) {
            return response()->json(['error' => 'Not available.'], 403);
        }

        $result = CabinetService::captchaGenerate();
        if (empty($result['ok'])) {
            return response()->json(['error' => 'Failed to load captcha.'], 502);
        }

        return response()->json($result['data']);
    }

    /**
     * Проверка решения пазла. Успех — одноразовый токен в сессию сервера
     * (в браузер не отдаём), дальше им подписывается embedded/start.
     */
    public function captchaVerify(Request $request)
    {
        if (!CabinetService::enabled() || !CabinetService::shopSettings()['embedded_login']) {
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
     * Встроенный логин, шаг 1: email + решённый пазл кабинета → кабинет шлёт код.
     */
    public function embeddedStart(Request $request)
    {
        if (!CabinetService::enabled()) {
            return redirect()->route('home.login');
        }
        if (session('cabinet.access_token')) {
            return redirect()->route('cabinet.live.page', ['page' => 'orders']);
        }
        $settings = CabinetService::shopSettings();
        if (empty($settings['embedded_login'])) {
            return redirect()->route('account.index');
        }

        $email = trim((string) $request->input('email', ''));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('cabinet.login', ['step' => 'email'])
                ->with('cabinet_embedded_error', __('text.errors_wrong_email'))
                ->withInput(['email' => $email]);
        }

        // Одноразовый токен пазла из сессии (кладётся captchaVerify выше).
        // Сжигаем только на выходе с этого шага: попытка direct ниже при
        // отказе НЕ должна его трогать — он ещё пригодится для входа с кодом.
        // Разовый пропуск (?bypass= из админки, тоже в сессии) живёт по тем
        // же правилам: используем здесь — и забываем на любом выходе.
        $captchaToken = (string) session('cabinet.captcha_token', '');
        $bypassToken = (string) session('cabinet.bypass_token', '');
        if ($captchaToken === '') {
            return redirect()->route('cabinet.login', ['step' => 'email'])
                ->with('cabinet_embedded_error', __('text.cabinet_captcha_required'))
                ->withInput(['email' => $email]);
        }

        // Прямой вход без кода — для запросов шопа с админского IP кабинета
        // либо по разовому пропуску из админки. Остальным портал отвечает 403
        // direct_login_disabled — молча идём обычным путём с кодом (и при
        // rate_limited тоже: у start свой лимит, пользователю прерываться незачем).
        $direct = CabinetService::embeddedDirect($email, $request->ip(), $captchaToken, $this->browserFingerprint($request), $bypassToken !== '' ? $bypassToken : null);
        if (!empty($direct['ok'])) {
            session()->forget(['cabinet.captcha_token', 'cabinet.bypass_token']);
            $this->storePair((array) $direct['data']);

            return redirect()->route('cabinet.live.page', ['page' => 'orders']);
        }
        if (!\in_array((string) ($direct['code'] ?? ''), ['direct_login_disabled', 'rate_limited'], true)) {
            session()->forget(['cabinet.captcha_token', 'cabinet.bypass_token']);
            $message = $this->embeddedErrorMessage((string) ($direct['code'] ?? ''), (string) ($direct['error'] ?? ''), null);

            return redirect()->route('cabinet.login', ['step' => 'email'])
                ->with('cabinet_embedded_error', $message)
                ->withInput(['email' => $email]);
        }

        $result = CabinetService::embeddedStart($email, $request->ip(), $captchaToken, $this->browserFingerprint($request));
        session()->forget(['cabinet.captcha_token', 'cabinet.bypass_token']);
        if (empty($result['ok'])) {
            $message = $this->embeddedErrorMessage((string) ($result['code'] ?? ''), (string) ($result['error'] ?? ''), isset($result['retry_after']) ? (int) $result['retry_after'] : null);

            return redirect()->route('cabinet.login', ['step' => 'email'])
                ->with('cabinet_embedded_error', $message)
                ->withInput(['email' => $email]);
        }

        session(['cabinet.embedded_email' => $email]);

        return redirect()->route('cabinet.login', ['step' => 'code']);
    }

    /**
     * Встроенный логин, шаг 2: проверка кода → пара в сессию.
     */
    public function embeddedVerify(Request $request)
    {
        if (!CabinetService::enabled()) {
            return redirect()->route('home.login');
        }
        if (session('cabinet.access_token')) {
            return redirect()->route('cabinet.live.page', ['page' => 'orders']);
        }

        $email = (string) session('cabinet.embedded_email', '');
        $settings = CabinetService::shopSettings();
        if ($email === '' || empty($settings['embedded_login'])) {
            return redirect()->route('account.index');
        }

        // Сюда попали — значит разовый пропуск не сработал: забываем,
        // дальше обычный вход с кодом.
        session()->forget('cabinet.bypass_token');

        $code = (string) preg_replace('/\D/', '', (string) $request->input('code', ''));
        if (strlen($code) !== 6) {
            return redirect()->route('cabinet.login', ['step' => 'code'])
                ->with('cabinet_embedded_error', __('text.errors_wrong_code'));
        }

        $result = CabinetService::embeddedVerify($email, $code, $request->ip(), $this->browserFingerprint($request));
        if (empty($result['ok'])) {
            $resultCode = (string) ($result['code'] ?? '');
            if ($resultCode === 'too_many_attempts') {
                session()->forget('cabinet.embedded_email');

                return redirect()->route('cabinet.login', ['step' => 'email'])
                    ->with('cabinet_embedded_error', __('text.errors_too_many_attempts'));
            }

            return redirect()->route('cabinet.login', ['step' => 'code'])
                ->with('cabinet_embedded_error', __('text.errors_wrong_code'));
        }

        $this->storePair((array) $result['data']);
        session()->forget('cabinet.embedded_email');

        return redirect()->route('cabinet.live.page', ['page' => 'orders']);
    }

    /**
     * Fingerprint браузера из скрытого поля форм (считает DeviceLib):
     * шоп пробрасывает его в кабинет для привязки кода (детект перехвата).
     * Пусто — допустимо, тогда сверка по fingerprint пропускается.
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
            'cooldown'              => __('text.errors_code_cooldown', ['seconds' => (int) $retryAfter]),
            'embedded_disabled'     => __('text.errors_embedded_disabled'),
            'captcha_required'      => __('text.cabinet_captcha_required'),
            'connection_failed'     => __('text.errors_connection_failed'),
            default                 => $fallback !== '' ? $fallback : __('text.errors_login_failed'),
        };
    }

    /**
     * Старт логина: state + PKCE в сессию, дальше редирект или попап.
     */
    /**
     * Старт внешнего входа: state + PKCE в сессию, дальше прямой
     * редирект в кабинет (та же вкладка, без попапов).
     */
    public function start()
    {
        if (!CabinetService::enabled()) {
            return redirect()->route('home.login');
        }

        if (session('cabinet.access_token')) {
            return redirect()->route('cabinet.live.page', ['page' => 'orders']);
        }

        $cfg  = CabinetService::config();

        if ($cfg['secret'] === '') {
            return redirect()->route('account.index')
                ->with('cabinet_error', 'Cabinet is not configured: secret key is missing. Ask the shop administrator.');
        }

        $pkce  = CabinetService::pkcePair();
        $state = CabinetService::randomBase64Url(24);

        session([
            'cabinet.pkce_verifier' => $pkce['verifier'],
            'cabinet.login_state'   => $state,
        ]);

        $url = CabinetService::loginUrl(
            $cfg['url'],
            CabinetService::shopDomain(),
            $state,
            $pkce['challenge'],
            'S256',
            (string) session('design', config('app.design')),
            (string) session('locale', config('app.locale'))
        );

        return redirect()->away($url);
    }

    /**
     * Callback кабинета: ?token=&state=. Проверяем state,
     * меняем код сервер→сервер, кладём пару в сессию.
     */
    public function callback(Request $request)
    {
        $error = $this->finishLogin($request);

        if ($error !== null) {
            return redirect()->route('cabinet.login', ['step' => 'email'])
                ->with('cabinet_embedded_error', $this->humanError($error));
        }

        return redirect()->route('cabinet.live.page', ['page' => 'orders']);
    }

    /**
     * @return string|null null — успех, иначе код ошибки
     */
    private function finishLogin(Request $request): ?string
    {
        if (!CabinetService::enabled()) {
            return 'disabled';
        }

        $code     = trim((string) $request->query('token', (string) $request->query('code', '')));
        $state    = (string) $request->query('state', '');
        $expected = (string) session('cabinet.login_state', '');
        $verifier = (string) session('cabinet.pkce_verifier', '');

        session()->forget(['cabinet.login_state', 'cabinet.pkce_verifier']);

        if ($code === '' || $state === '' || $expected === '' || !hash_equals($expected, $state)) {
            return 'bad_state';
        }
        if ($verifier === '') {
            return 'session_expired';
        }

        $result = CabinetService::exchangeCode($code, $verifier);
        if (!$result['ok']) {
            Log::warning('Cabinet exchange failed: '.(string) $result['error']);

            return 'exchange_failed';
        }

        $this->storePair((array) $result['data']);

        return null;
    }

    private function humanError(string $code): string
    {
        return match ($code) {
            'bad_state'       => 'Login session mismatch, please try again.',
            'session_expired' => 'Login session expired, please try again.',
            'exchange_failed' => 'Cabinet did not confirm the login, please try again.',
            'disabled'        => 'Cabinet is disabled.',
            default           => 'Login failed, please try again.',
        };
    }

    private function storePair(array $pair): void
    {
        session([
            'cabinet.access_token'  => (string) ($pair['access_token'] ?? ''),
            'cabinet.refresh_token' => (string) ($pair['refresh_token'] ?? ''),
            'cabinet.expires_at'    => (string) ($pair['expires_at'] ?? ''),
        ]);
        // Новый вход — запреты скоупов и кеш capabilities сбрасываем.
        session()->forget('cabinet.forbidden');
        CabinetService::forgetCapabilities();
    }

    private function forgetTokens(): void
    {
        session()->forget(['cabinet.access_token', 'cabinet.refresh_token', 'cabinet.expires_at', 'cabinet.email', 'cabinet.forbidden']);
        CabinetService::forgetCapabilities();
    }

    public function logout()
    {
        $access = (string) session('cabinet.access_token', '');
        if ($access !== '') {
            CabinetService::logoutRemote($access);
        }
        $this->forgetTokens();

        return redirect()->route('home.index');
    }

    // ---------- JSON-прокси к /api/v1/* ----------

    public function apiMe(Request $request)
    {
        return $this->proxy($request, 'GET', 'me');
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
        // 1:1 с порталом (Orders.vue): фильтр по группе статусов.
        $status = (string) $request->query('status', '');
        if (in_array($status, ['checking', 'approved', 'delivered', 'declined', 'refunded'], true)) {
            $query['status'] = $status;
        }

        return $this->proxy($request, 'GET', 'orders', ['query' => $query]);
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
     * Повтор заказа локально, без обращений к API кабинета:
     * позиции заказа (с packagingId из /api/v1/orders) кладём в корзину
     * шопа и отдаём URL чекаута. Несопоставленные/скрытые упаковки молча
     * пропускаем; если не легло ничего — ошибка для тоста.
     */
    public function apiOrderRepeatVirtual(Request $request, int $id)
    {
        if (!CabinetService::enabled()) {
            return response()->json(['status' => 'error', 'message' => 'Cabinet is disabled.'], 403);
        }
        if ((string) session('cabinet.access_token', '') === '') {
            return response()->json(['status' => 'error', 'message' => 'Not logged in.', 'login' => false], 401);
        }

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
            return response()->json(['status' => 'error', 'message' => __('text.cabinet_repeat_failed')], 422);
        }

        Cart::update_cart_total();

        // Форму чекаута сразу заполняем адресом исходного заказа
        // (ключи session('form.*'), best effort — корзина уже собрана).
        $this->prefillCheckoutForm($request, $id);

        return response()->json(['status' => 'success', 'checkoutUrl' => route('checkout.index')]);
    }

    /**
     * Адрес доставки заказа из кабинета → session('form.*') для чекаута.
     * Best effort: нет скоупа/сети — молча пропускаем, повтор уже работает.
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
        // Все сессии отозваны — локальные токены тоже мертвы.
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
     * Публичный ключ E2EE портала для браузера (релей без авторизации).
     */
    public function apiE2eeKeys()
    {
        if (!CabinetService::enabled()) {
            return response()->json(['error' => 'Cabinet is disabled.'], 403);
        }

        $result = CabinetService::e2eeKeys();
        if (empty($result['ok'])) {
            return response()->json(['error' => 'E2EE is not available.'], 503);
        }

        return response()->json((array) $result['data']);
    }

    /**
     * Сквозные E2EE-заголовки браузера в кабинет (allowlist, без чтения).
     *
     * @return array<string, string>
     */
    private function e2eeForwardHeaders(Request $request): array
    {
        if (trim((string) $request->header('X-E2EE', '')) !== '1') {
            return [];
        }
        $headers = ['X-E2EE' => '1'];
        $epk = trim((string) $request->header('X-E2EE-Epk', ''));
        if ($epk !== '') {
            $headers['X-E2EE-Epk'] = substr($epk, 0, 64);
        }
        $kb = trim((string) $request->header('X-E2EE-Kb', ''));
        if ($kb === '1' || $kb === '0') {
            $headers['X-E2EE-Kb'] = $kb;
        }

        return $headers;
    }

    /**
     * Прокси с Bearer из серверной сессии + проброс X-FP браузера
     * как X-Fingerprint (токен кабинета к нему привязан).
     * E2EE-заголовки и тела-конверты ретранслируются вслепую
     * (шоп их не читает и не логирует, см. e2ee.js).
     * При 401 — одна ротация пары и повтор.
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

        $fingerprint = trim((string) $request->header('X-FP', ''));
        $options['headers'] = $this->e2eeForwardHeaders($request);

        $response = CabinetService::apiCall($method, $path, $access, $fingerprint, $options);

        if ($response->status() === 401) {
            $refresh = (string) session('cabinet.refresh_token', '');
            if ($refresh !== '') {
                $renewed = CabinetService::refreshPair($refresh);
                if (!empty($renewed['ok'])) {
                    $this->storePair((array) $renewed['data']);
                    $access   = (string) session('cabinet.access_token', '');
                    $response = CabinetService::apiCall($method, $path, $access, $fingerprint, $options);
                }
            }
            if ($response->status() === 401) {
                $this->forgetTokens();

                return response()->json(['error' => 'Session expired, please log in again.', 'login' => false], 401);
            }
        }

        // Скоуп выключен у API-клиента (403 от кабинета) — запоминаем,
        // чтобы прятать соответствующие разделы в навигации.
        if ($response->status() === 403 && ($scope = CabinetService::scopeForPath($path, $method)) !== null) {
            session(['cabinet.forbidden.'.$scope => true]);
        }

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type', 'application/json'));
    }
}
