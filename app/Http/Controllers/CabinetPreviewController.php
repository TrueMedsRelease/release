<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Language;
use App\Models\PhoneCodes;
use App\Services\ProductServices;
use Illuminate\Support\Facades\DB;
use Phattarachai\LaravelMobileDetect\Agent;

/**
 * Статические превью личного кабинета в лейауте магазина.
 * Без авторизации, без реальных данных кабинета — только моки для верстки.
 * Пилот: design_10. Масштабируется на остальные дизайны тем же шаблоном.
 *
 * Роуты:
 *   /preview/cabinet/{design}/{page}  page = orders|bonus|referral|contact
 *   /preview/cabinet-login/{design}/{step}  step = email|captcha|code
 */
class CabinetPreviewController extends Controller
{
    private const PAGES = ['orders', 'bonus', 'referral', 'contact', 'chat'];

    private const LOGIN_STEPS = ['email', 'captcha', 'code'];

    public function page(string $design, string $page = 'orders')
    {
        $design = $this->resolveDesign($design);
        if ($design === null || !in_array($page, self::PAGES, true)) {
            abort(404);
        }
        if (!view()->exists($design . '.cabinet.' . $page)) {
            abort(404);
        }

        return view($design . '.cabinet.' . $page, array_merge(
            $this->layoutData($design, 'cabinet_' . $page),
            $this->mockData(),
            ['cabp_page' => $page]
        ));
    }

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

        return view($design . '.cabinet.live-' . $page, array_merge(
            $this->layoutData($design, 'cabinet_' . $page),
            ['cabp_page' => $page]
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
     * Хром магазина (меню/бестселлеры/телефоны) — реальный, данные
     * кабинета — моки (см. mockData).
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

    /**
     * Мок-данные кабинета: структура 1:1 с боевыми account.*,
     * значения выдуманные, без привязки к API.
     */
    private function mockData(): array
    {
        return [
            'cabp_user' => [
                'initials' => 'JD',
                'name' => 'John Doe',
                'id_label' => 'ID 123456',
                'bonus' => '$24.50',
                'email' => 'john.doe@example.com',
            ],
            'cabp_filters' => [
                ['key' => 'all', 'label' => 'All'],
                ['key' => 'checking', 'label' => 'Checking'],
                ['key' => 'approved', 'label' => 'Approved'],
                ['key' => 'delivered', 'label' => 'Delivered'],
                ['key' => 'declined', 'label' => 'Declined'],
                ['key' => 'refunded', 'label' => 'Refunded'],
            ],
            'cabp_orders' => [
                [
                    'id' => 104281,
                    'date' => '2026-09-02',
                    'status_key' => 'delivered',
                    'status' => 'Delivered',
                    'bonus' => 4.20,
                    'total' => '$89.90',
                    'next_date' => '',
                    'items' => [
                        ['name' => 'Viagra 100mg × 30 pills', 'qty' => 1, 'sum' => '$69.95'],
                        ['name' => 'Shipping (EMS)', 'qty' => 1, 'sum' => '$19.95'],
                    ],
                    'tracking_number' => 'RN123456789NL',
                    'tracking' => [
                        'destination' => 'United Kingdom',
                        'days' => 9,
                        'latest' => '2026-09-08 14:22',
                        'tracks' => [
                            [
                                'number' => 'RN123456789NL',
                                'from' => 'Netherlands',
                                'to' => 'United Kingdom',
                                'latest' => 'Departed transit facility, London',
                                'latest_time' => '2026-09-08 14:22',
                                'providers' => [
                                    [
                                        'name' => 'PostNL',
                                        'country' => 'NL',
                                        'sync' => '2026-09-08 14:30',
                                        'events' => [
                                            ['time' => '2026-09-08 14:22', 'text' => 'London, Departed transit facility'],
                                            ['time' => '2026-09-07 09:05', 'text' => 'Amsterdam, Arrived at sorting center'],
                                            ['time' => '2026-09-05 18:40', 'text' => 'Amsterdam, Accepted by carrier'],
                                            ['time' => '2026-09-04 11:12', 'text' => 'The Hague, Label created'],
                                            ['time' => '2026-09-03 08:00', 'text' => 'The Hague, Booking confirmed'],
                                            ['time' => '2026-09-02 16:45', 'text' => 'Shop, Order handed to carrier'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 104017,
                    'date' => '2026-08-14',
                    'status_key' => 'approved',
                    'status' => 'Approved',
                    'bonus' => 2.10,
                    'total' => '$54.95',
                    'next_date' => 'Next date of order: 2026-10-14',
                    'items' => [
                        ['name' => 'Cialis 20mg × 12 pills', 'qty' => 1, 'sum' => '$44.95'],
                        ['name' => 'Shipping (Standard)', 'qty' => 1, 'sum' => '$10.00'],
                    ],
                    'tracking_number' => '',
                    'recurring' => true,
                    'billing_url' => 'https://billing.example.com/pay/104017',
                ],
                [
                    'id' => 103552,
                    'date' => '2026-07-28',
                    'status_key' => 'checking',
                    'status' => 'Checking',
                    'bonus' => 0,
                    'total' => '$34.95',
                    'next_date' => '',
                    'items' => [
                        ['name' => 'Levitra 20mg × 8 pills', 'qty' => 1, 'sum' => '$34.95'],
                    ],
                    'tracking_number' => '',
                    'shop' => 'https://example-shop.com/',
                ],
            ],
            'cabp_ship' => [
                'caption' => 'Shipping to',
                'name' => 'John Doe',
                'address' => '221B Baker Street, Apt 4',
                'city' => 'London NW1 6XE',
                'country' => 'United Kingdom',
            ],
            'cabp_bonus_card' => [
                'number' => 'BON-5521-0093',
                'status' => 'Gold',
                'balance' => '$24.50',
                'expiring' => [
                    ['date' => '2026-10-01', 'amount' => '$6.00'],
                    ['date' => '2026-11-01', 'amount' => '$3.50'],
                ],
            ],
            'cabp_bonus_history' => [
                ['date' => '2026-09-02', 'type' => 'Accrue (Order: 104281)', 'delta' => '+$4.20', 'positive' => true],
                ['date' => '2026-08-14', 'type' => 'Accrue (Order: 104017)', 'delta' => '+$2.10', 'positive' => true],
                ['date' => '2026-07-30', 'type' => 'Debit (Order: 103552)', 'delta' => '−$5.00', 'positive' => false],
                ['date' => '2026-07-10', 'type' => 'Accrue Referral', 'delta' => '+$10.00', 'positive' => true],
                ['date' => '2026-06-22', 'type' => 'Expire', 'delta' => '−$1.50', 'positive' => false],
            ],
            'cabp_referral' => [
                'link' => 'https://example-shop.com/?ref=BON-5521-0093',
                'coupon' => 'FRIEND-10-OFF',
            ],
            'cabp_chat' => [
                'send_limit' => 3,
                'messages' => [
                    [
                        'from' => 'support',
                        'author' => 'Support',
                        'date' => '2026-09-08 14:22',
                        'subject' => 'Order #104281',
                        'text' => 'Your order has been shipped via EMS. Tracking number: RN123456789NL.',
                    ],
                    [
                        'from' => 'me',
                        'author' => '',
                        'date' => '2026-09-08 15:03',
                        'subject' => '',
                        'text' => 'Thanks! How long does delivery usually take?',
                    ],
                    [
                        'from' => 'support',
                        'author' => 'Support',
                        'date' => '2026-09-08 15:20',
                        'subject' => 'Re: delivery time',
                        'text' => 'Usually 7–12 business days to the UK. Bonus points will land on your card once delivered.',
                    ],
                    [
                        'from' => 'me',
                        'author' => '',
                        'date' => '2026-09-09 09:12',
                        'subject' => 'Change address',
                        'text' => 'Can I still change the shipping address for order #104017?',
                    ],
                ],
            ],
        ];
    }
}
