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
 * Live cabinet and login in the current design layout (design from session).
 *
 * Routes:
 *   /account/{page}      page = orders|bonus|referral|contact|chat
 *   /account/login/{step} step = email|captcha|code
 */
class CabinetPreviewController extends Controller
{
    private const PAGES = ['orders', 'bonus', 'referral', 'contact', 'chat'];

    private const LOGIN_STEPS = ['email', 'captcha', 'code'];

    public function login(string $design, string $step = 'email')
    {
        $design = $this->resolveDesign($design);
        // Legacy .../login route leads to the first step.
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
            return redirect()->route('account.page', ['page' => CabinetService::firstVisiblePage()]);
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
     * Live cabinet of the current design: design from session (like other
     * shop pages), not present in the URL. Example: /account/orders
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
     * Cabinet login of the current design. Example: /account/login/email
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
     * Live cabinet in the shop layout: same sections, data via
     * /account/api/* (Alpine). No tokens — to the login page.
     * Capabilities come from the 60s cache to hide disabled sections at once
     * (instead of a 403 flash reading "section unavailable").
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
            return redirect()->route('account.login', ['step' => 'email']);
        }

        // Disabled in the shop display settings — same as a missing section.
        $cabDisplay = CabinetService::displayConfig();
        if (empty($cabDisplay['pages'][$page])) {
            abort(404);
        }

        // Instant hiding of disabled sections: capabilities prefetch (60s cache)
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
            ['cabp_page' => $page, 'cabCapabilities' => $capabilities, 'cabDisplay' => $cabDisplay]
        ));
    }

    /**
     * Only existing designs with a shop layout.
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
     * Current shop design: session, otherwise the app default —
     * the same source as other pages (see HomeController).
     * Overridden via ?design=N through SessionParameterHandler.
     */
    private function currentDesign(): ?string
    {
        return $this->resolveDesign((string) session('design', config('app.design')));
    }

    /**
     * Minimal variable set required by layouts.main.
     * Shop chrome (menu/bestsellers/phones) is real.
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
