<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Language;
use App\Models\PhoneCodes;
use App\Services\CabinetService;
use App\Services\ProductServices;
use Illuminate\Support\Facades\DB;
use Phattarachai\LaravelMobileDetect\Agent;

/**
 * Живой кабинет и вход в лейауте текущего дизайна (дизайн из сессии).
 *
 * Роуты:
 *   /cabinet/{page?}      page = orders|bonus|referral|contact|chat
 *   /cabinet-login/{step?} step = email|captcha|code
 */
class CabinetPreviewController extends Controller
{
    private const PAGES = ['orders', 'bonus', 'referral', 'contact', 'chat'];

    private const LOGIN_STEPS = ['email', 'captcha', 'code'];

    public function login(string $design, string $step = 'email')
    {
        $design = $this->resolveDesign($design);
        // Старый роут .../login ведём на первый шаг.
        if ($step === 'login') {
            $step = 'email';
        }
        if ($design === null) {
            abort(404);
        }
        if (!in_array($step, self::LOGIN_STEPS, true)) {
            abort(404);
        }

        $view = 'login-' . $step;
        if (!view()->exists($design . '.cabinet.' . $view)) {
            abort(404);
        }
        if ((string) session('cabinet.access_token', '') !== '') {
            return redirect()->route('cabinet.live.page', ['page' => 'orders']);
        }

        return view($design . '.cabinet.' . $view, array_merge(
            $this->layoutData($design, 'cabinet_login'),
            [
                'cabp_page' => 'login',
                'cabp_step' => $step,
                'email' => (string) session('cabinet.embedded_email', ''),
            ]
        ));
    }

    /**
     * Живой кабинет текущего дизайна: дизайн из сессии (как остальные
     * страницы магазина), в URL не указывается. Пример: /cabinet/orders
     */
    public function cabinetPage(string $page = 'orders')
    {
        $design = $this->currentDesign();
        if ($design === null) {
            abort(404);
        }

        return $this->live($design, $page);
    }

    /**
     * Вход в кабинет текущего дизайна. Пример: /cabinet-login/email
     */
    public function cabinetLogin(string $step = 'email')
    {
        $design = $this->currentDesign();
        if ($design === null) {
            abort(404);
        }

        return $this->login($design, $step);
    }

    /**
     * Живой кабинет в лейауте магазина: те же разделы, данные — через
     * /account/api/* (Alpine). Без токенов — на страницу входа.
     * Capabilities — из кеша 60с, чтобы скрывать недоступные разделы сразу
     * (а не после 403 и мигания «раздел не доступен»).
     */
    public function live(string $design, string $page = 'orders')
    {
        $design = $this->resolveDesign($design);
        if ($design === null || !in_array($page, self::PAGES, true)) {
            abort(404);
        }
        if (!view()->exists($design . '.cabinet.live-' . $page)) {
            abort(404);
        }
        if ((string) session('cabinet.access_token', '') === '') {
            return redirect()->route('cabinet.login', ['step' => 'email']);
        }

        // Мгновенное скрытие отключенных разделов: префетч capabilities (кеш 60с)
        $capabilities = CabinetService::capabilities();
        if (is_array($capabilities) && isset($capabilities['scopes']) && is_array($capabilities['scopes'])) {
            $scopeMap = [
                'orders' => 'orders',
                'bonus' => 'bonus',
                'referral' => 'referral',
                'contact' => 'contact',
                'chat' => 'chat',
            ];
            $need = $scopeMap[$page] ?? null;
            if ($need !== null && !in_array($need, $capabilities['scopes'], true)) {
                abort(404);
            }
            if ($page === 'chat' && empty($capabilities['chat_enabled'])) {
                abort(404);
            }
        }

        return view($design . '.cabinet.live-' . $page, array_merge(
            $this->layoutData($design, 'cabinet_' . $page),
            ['cabp_page' => $page, 'cabCapabilities' => $capabilities]
        ));
    }

    /**
     * Только существующие дизайны с layout магазина.
     */
    private function resolveDesign(string $design): ?string
    {
        if (!preg_match('/^design_\d+$/', $design)) {
            return null;
        }
        if (!view()->exists($design . '.layouts.main')) {
            return null;
        }

        return $design;
    }

    /**
     * Текущий дизайн магазина: сессия, иначе дефолт приложения —
     * тот же источник, что у остальных страниц (см. HomeController).
     * Переопределяется ?design=N через SessionParameterHandler.
     */
    private function currentDesign(): ?string
    {
        return $this->resolveDesign((string) session('design', config('app.design')));
    }

    /**
     * Минимальный набор переменных, который требует layouts.main.
     * Хром магазина (меню/бестселлеры/телефоны) — реальный.
     */
    private function layoutData(string $design, string $pageKey): array
    {
        $agent = new Agent();

        try {
            $phoneCodes = PhoneCodes::all()->toArray();
        } catch (\Throwable) {
            $phoneCodes = [];
        }
        try {
            $bestsellers = ProductServices::GetBestsellers($design);
        } catch (\Throwable) {
            $bestsellers = [];
        }
        try {
            $menu = ProductServices::GetCategoriesWithProducts($design);
        } catch (\Throwable) {
            $menu = [];
        }
        try {
            $firstLetters = ProductServices::getFirstLetters();
        } catch (\Throwable) {
            $firstLetters = [];
        }
        try {
            $pageProperties = ProductServices::getPageProperties('contact_us');
        } catch (\Throwable) {
            $pageProperties = (object) ['title' => 'Cabinet preview', 'keyword' => '', 'description' => ''];
        }

        try {
            $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        } catch (\Throwable) {
            $pixels = [];
        }
        $pixel = '';
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel ?? '') . "\n\n";
        }

        $domain = str_replace(['http://', 'https://'], '', (string) env('APP_URL', 'localhost'));
        if ($domain !== '' && substr($domain, -1) === '/') {
            $domain = substr($domain, 0, -1);
        }

        try {
            $codes = HomeController::getAllCountryISO();
            foreach ($codes as $i => $code) {
                $codes[$i] = strtolower($code->iso);
            }
            $codes = json_encode($codes);
        } catch (\Throwable) {
            $codes = '[]';
        }

        $device = 'desktop';
        try {
            $device = ProductServices::getDevice($agent);
        } catch (\Throwable) {
        }

        $webStatistic = ['params_string' => 'preview=1&page=' . $pageKey . '&device=' . $device];

        return [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'phone_codes' => $phoneCodes,
            'page_properties' => $pageProperties,
            'cur_category' => '',
            'agent' => $agent,
            'Language' => Language::class,
            'Currency' => Currency::class,
            'pixel' => $pixel,
            'first_letters' => $firstLetters,
            'domain' => $domain,
            'web_statistic' => $webStatistic,
            'codes' => $codes,
        ];
    }

}
