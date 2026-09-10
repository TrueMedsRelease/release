<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductPackaging;
use App\Services\CabinetService;
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
     * Хаб: есть токены — заказы, нет — экран входа.
     * Вход либо полностью у себя (разрешён в настройках интеграции
     * кабинета, кеш CabinetService::shopSettings), либо мгновенный уход
     * на кабинет. Промежуточный экран показываем только с ошибкой
     * (иначе авторедирект зациклился бы: callback → index → start → …).
     */
    public function index()
    {
        if (!CabinetService::enabled()) {
            return redirect()->route('home.login');
        }

        if (session('cabinet.access_token')) {
            return redirect()->route('account.orders');
        }

        $settings = CabinetService::shopSettings();
        if ($settings['embedded_login']) {
            // Пропуск 2FA у клиента — вход без кода из письма вообще.
            if (!empty($settings['skip_2fa'])) {
                return view('account.embedded-direct', [
                    'email' => (string) session('cabinet.embedded_email', ''),
                ]);
            }

            return view('account.embedded', [
                'email' => (string) session('cabinet.embedded_email', ''),
            ]);
        }

        if (session('cabinet_error')) {
            return view('account.start', [
                'cabinet_error' => (string) session('cabinet_error', ''),
            ]);
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
            return redirect()->route('account.orders');
        }
        $settings = CabinetService::shopSettings();
        if (empty($settings['embedded_login'])) {
            return redirect()->route('account.index');
        }
        // При пропуске 2FA шаг с кодом не существует — только прямой вход.
        if (!empty($settings['skip_2fa'])) {
            return redirect()->route('account.index');
        }

        $email = trim((string) $request->input('email', ''));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('account.index')
                ->with('cabinet_embedded_error', __('text.errors_wrong_email'))
                ->withInput(['email' => $email]);
        }

        // Одноразовый токен пазла из сессии (кладётся captchaVerify выше).
        $captchaToken = (string) session('cabinet.captcha_token', '');
        session()->forget('cabinet.captcha_token');
        if ($captchaToken === '') {
            return redirect()->route('account.index')
                ->with('cabinet_embedded_error', __('text.cabinet_captcha_required'))
                ->withInput(['email' => $email]);
        }

        $result = CabinetService::embeddedStart($email, $request->ip(), $captchaToken);
        if (empty($result['ok'])) {
            $message = $this->embeddedErrorMessage((string) ($result['code'] ?? ''), (string) ($result['error'] ?? ''), isset($result['retry_after']) ? (int) $result['retry_after'] : null);

            return redirect()->route('account.index')
                ->with('cabinet_embedded_error', $message)
                ->withInput(['email' => $email]);
        }

        session(['cabinet.embedded_email' => $email]);

        return redirect()->route('account.embedded.code');
    }

    /**
     * Встроенный логин, шаг 2: форма кода.
     */
    public function embeddedCode()
    {
        if (!CabinetService::enabled()) {
            return redirect()->route('home.login');
        }
        if (session('cabinet.access_token')) {
            return redirect()->route('account.orders');
        }
        $settings = CabinetService::shopSettings();
        if (empty($settings['embedded_login']) || !empty($settings['skip_2fa'])) {
            return redirect()->route('account.index');
        }
        if ((string) session('cabinet.embedded_email', '') === '') {
            return redirect()->route('account.index');
        }

        return view('account.embedded-code', [
            'email' => (string) session('cabinet.embedded_email', ''),
        ]);
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
            return redirect()->route('account.orders');
        }

        $email = (string) session('cabinet.embedded_email', '');
        $settings = CabinetService::shopSettings();
        if ($email === '' || empty($settings['embedded_login']) || !empty($settings['skip_2fa'])) {
            return redirect()->route('account.index');
        }

        $code = (string) preg_replace('/\D/', '', (string) $request->input('code', ''));
        if (strlen($code) !== 6) {
            return redirect()->route('account.embedded.code')
                ->with('cabinet_embedded_error', __('text.errors_wrong_code'));
        }

        $result = CabinetService::embeddedVerify($email, $code, $request->ip());
        if (empty($result['ok'])) {
            $resultCode = (string) ($result['code'] ?? '');
            if ($resultCode === 'too_many_attempts') {
                session()->forget('cabinet.embedded_email');

                return redirect()->route('account.index')
                    ->with('cabinet_embedded_error', __('text.errors_too_many_attempts'));
            }

            return redirect()->route('account.embedded.code')
                ->with('cabinet_embedded_error', __('text.errors_wrong_code'));
        }

        $this->storePair((array) $result['data']);
        session()->forget('cabinet.embedded_email');

        return redirect()->route('account.orders');
    }

    /**
     * Встроенный логин без кода из письма (только при skip_2fa у клиента):
     * email + пазл → сразу пара токенов, шага с кодом нет вообще.
     */
    public function embeddedDirect(Request $request)
    {
        if (!CabinetService::enabled()) {
            return redirect()->route('home.login');
        }
        if (session('cabinet.access_token')) {
            return redirect()->route('account.orders');
        }
        $settings = CabinetService::shopSettings();
        if (empty($settings['embedded_login']) || empty($settings['skip_2fa'])) {
            return redirect()->route('account.index');
        }

        $email = trim((string) $request->input('email', ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('account.index')
                ->with('cabinet_embedded_error', __('text.errors_wrong_email'))
                ->withInput(['email' => $email]);
        }

        // Одноразовый токен пазла из сессии (кладётся captchaVerify выше).
        $captchaToken = (string) session('cabinet.captcha_token', '');
        session()->forget('cabinet.captcha_token');
        if ($captchaToken === '') {
            return redirect()->route('account.index')
                ->with('cabinet_embedded_error', __('text.cabinet_captcha_required'))
                ->withInput(['email' => $email]);
        }

        $result = CabinetService::embeddedDirect($email, $request->ip(), $captchaToken);
        if (empty($result['ok'])) {
            $message = $this->embeddedErrorMessage((string) ($result['code'] ?? ''), (string) ($result['error'] ?? ''), null);

            return redirect()->route('account.index')
                ->with('cabinet_embedded_error', $message)
                ->withInput(['email' => $email]);
        }

        $this->storePair((array) $result['data']);

        return redirect()->route('account.orders');
    }

    private function embeddedErrorMessage(string $code, string $fallback, ?int $retryAfter): string
    {
        return match ($code) {
            'unknown_email'         => __('text.errors_unknown_email'),
            'cooldown'              => __('text.errors_code_cooldown', ['seconds' => (int) $retryAfter]),
            'embedded_disabled'     => __('text.errors_embedded_disabled'),
            'direct_login_disabled' => __('text.errors_direct_disabled'),
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
            return redirect()->route('account.orders');
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

        $url = CabinetService::loginUrl($cfg['url'], CabinetService::shopDomain(), $state, $pkce['challenge']);

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
            return redirect()->route('account.index')->with('cabinet_error', $this->humanError($error));
        }

        return redirect()->route('account.orders');
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
        // Новый вход — запреты скоупов проверяем заново.
        session()->forget('cabinet.forbidden');
    }

    private function forgetTokens(): void
    {
        session()->forget(['cabinet.access_token', 'cabinet.refresh_token', 'cabinet.expires_at', 'cabinet.email', 'cabinet.forbidden']);
    }

    /**
     * Страница "My orders" (Alpine-приложение, данные через прокси ниже).
     */
    public function orders()
    {
        return $this->page('account.orders');
    }

    public function bonus()
    {
        return $this->page('account.bonus');
    }

    public function referral()
    {
        return $this->page('account.referral');
    }

    public function sessions()
    {
        return $this->page('account.sessions');
    }

    public function contact()
    {
        return $this->page('account.contact');
    }

    /**
     * Общая страница кабинета: требует включённого флага и токенов.
     */
    private function page(string $view)
    {
        if (!CabinetService::enabled()) {
            return redirect()->route('home.login');
        }
        if (!session('cabinet.access_token')) {
            return redirect()->route('account.index');
        }

        return view($view, [
            'email'       => (string) session('cabinet.email', ''),
            'cabinet_url' => CabinetService::cabinetUrl(),
            'cabinet_fp'  => rtrim(CabinetService::cabinetUrl(), '/').'/f.js',
        ]);
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

        return response()->json(['status' => 'success', 'checkoutUrl' => route('checkout.index')]);
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

    /**
     * Прокси с Bearer из серверной сессии + проброс X-FP браузера
     * как X-Fingerprint (токен кабинета к нему привязан).
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
        if ($response->status() === 403 && ($scope = CabinetService::scopeForPath($path)) !== null) {
            session(['cabinet.forbidden.'.$scope => true]);
        }

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type', 'application/json'));
    }
}
