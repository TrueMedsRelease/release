<?php

namespace Tests\Feature;

use App\Services\CabinetService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CabinetPreviewTest extends TestCase
{
    /**
     * Note: in the sandbox APP_URL has no scheme (testshop.test),
     * so the FIRST url() in a fresh app builds a broken URI
     * and the first HTTP request lands in fallback-404. Hence the warm-up
     * request first. In prod with a proper APP_URL and a real web server
     * (normal REQUEST_URI) this effect does not exist.
     */
    public function test_live_redirects_to_design_login_when_no_token(): void
    {
        $this->get('/account/login/email'); // warm up UrlGenerator
        $this->seedShopSettings();

        try {
            foreach (['orders', 'bonus', 'referral', 'contact', 'chat'] as $page) {
                $response = $this->get('/account/' . $page);
                $response->assertRedirect(route('account.login', ['step' => 'email']));
            }
        } finally {
            $this->forgetShopSettings();
        }
    }

    public function test_designless_login_renders(): void
    {
        $this->get('/account/login/email'); // warm up UrlGenerator
        $this->seedShopSettings();

        try {
            foreach (['email', 'captcha', 'code'] as $step) {
                $response = $this->get('/account/login/' . $step);
                $response->assertStatus(200);
                $response->assertSee('cablogin-', false);
            }
            // No preview pager anymore; a working language block instead of the language stub.
            $html = $this->get('/account/login/email')->getContent();
            $this->assertStringNotContainsString('cablogin-pager', $html);
            $this->assertStringContainsString('cablogin-header__lang', $html);
        } finally {
            $this->forgetShopSettings();
        }
    }

    public function test_unknown_design_404_on_designless_routes(): void
    {
        $this->get('/account/login/email'); // warm up UrlGenerator

        $response = $this->withSession(['design' => 'design_99'])->get('/account/orders');
        $response->assertNotFound();

        $response = $this->withSession(['design' => 'design_99'])->get('/account/login/email');
        $response->assertNotFound();
    }

    public function test_cabinet_nav_contains_logout_form(): void
    {
        $html = view('cabinet-live.nav-live', [
            'cabp_page' => 'orders',
            'cabCapabilities' => ['scopes' => ['orders', 'bonus', 'referral', 'contact', 'chat'], 'chat_enabled' => true],
            'design' => 'design_10',
        ])->render();

        $this->assertStringContainsString(route('account.logout'), $html);
        $this->assertStringContainsString(__('text.cabinet_logout'), $html);
    }

    public function test_logout_clears_session_and_redirects_home(): void
    {
        $this->get('/account/login/email'); // warm up UrlGenerator
        \Illuminate\Support\Facades\Http::fake();

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->withSession([
                'cabinet.access_token' => 'test-token',
                'cabinet.refresh_token' => 'test-refresh',
            ])
            ->post(route('account.logout'));

        $response->assertRedirect(route('home.index'));
        $response->assertSessionMissing('cabinet.access_token');
        $response->assertSessionMissing('cabinet.refresh_token');
    }

    public function test_login_layout_uses_custom_alpine_lang_dropdown(): void
    {
        $this->get('/account/login/email'); // warm up UrlGenerator
        $html = view('design_10.cabinet.login-layout', [
            'design' => 'design_10',
            'Language' => CabinetLoginLayoutFakeLanguage::class,
        ])->render();

        $this->assertStringNotContainsString('<select', $html);
        $this->assertStringContainsString('x-data="cabinetLangSelect()"', $html);
        $this->assertStringContainsString('select__options', $html);
        $this->assertStringContainsString('Deutsch', $html);
        $this->assertStringContainsString(route('home.language', 'de'), $html);
        $this->assertStringContainsString('js/cabinet/lang-select.js', $html);
        $this->assertStringContainsString('js/cabinet-alpine.min.js', $html);
    }
    /**
     * Network-free integration settings: cache bypasses both DB and cabinet.
     */
    private function seedShopSettings(): void
    {
        Cache::put(CabinetService::SETTINGS_CACHE_KEY, ['shop_id' => 'test', 'embedded_login' => true], 60);
    }

    private function forgetShopSettings(): void
    {
        Cache::forget(CabinetService::SETTINGS_CACHE_KEY);
    }

    /**
     * Every design with a cabinet has live and login views (no mocks).
     */
    public function test_all_designs_have_cabinet_views(): void
    {
        $designs = ['design_1', 'design_2', 'design_3', 'design_4', 'design_5', 'design_9', 'design_10', 'design_11', 'design_12', 'design_14', 'design_15', 'design_16', 'design_17'];
        $views = ['login-layout', 'login-email', 'login-captcha', 'login-code', 'live-orders', 'live-bonus', 'live-referral', 'live-contact', 'live-chat'];

        foreach ($designs as $design) {
            foreach ($views as $view) {
                $this->assertTrue(
                    view()->exists($design . '.cabinet.' . $view),
                    "View {$design}.cabinet.{$view} should exist"
                );
            }
        }

        foreach (['login-email-body', 'login-captcha-body', 'logincode-body'] as $partial) {
            $this->assertTrue(view()->exists('cabinet-parts.' . $partial), "Shared partial cabinet-parts.{$partial} should exist");
        }

        foreach (['nav-live', 'orders-live', 'bonus-live', 'referral-live', 'contact-live', 'chat-live'] as $partial) {
            $this->assertTrue(view()->exists('cabinet-live.' . $partial), "Shared partial cabinet-live.{$partial} should exist");
        }
    }

    public function test_nav_hides_page_disabled_in_display_settings(): void
    {
        $html = view('cabinet-live.nav-live', [
            'cabp_page' => 'orders',
            'cabCapabilities' => ['scopes' => ['orders', 'bonus', 'referral', 'contact', 'chat'], 'chat_enabled' => true],
            'cabDisplay' => array_replace_recursive(
                CabinetService::displayDefaults(),
                ['pages' => ['bonus' => false]]
            ),
            'design' => 'design_10',
        ])->render();

        $this->assertStringContainsString(route('account.page', ['page' => 'orders']), $html);
        $this->assertStringNotContainsString(route('account.page', ['page' => 'bonus']), $html);
    }

    public function test_orders_page_config_excludes_refunded_and_carries_display_flags(): void
    {
        $this->get('/account/login/email'); // warm up UrlGenerator
        Cache::put(
            CabinetService::SETTINGS_CACHE_KEY,
            ['shop_id' => 'test', 'embedded_login' => true, 'billing_base_url' => 'https://billing.example'],
            60
        );

        try {
            $response = $this->withSession(['cabinet.access_token' => 'test-token'])
                ->get('/account/orders');
            $response->assertStatus(200);

            $html = $response->getContent();
            $this->assertSame(1, preg_match('/window\.CabinetConfig = (\{.*?\});/s', $html, $matches));
            $config = json_decode($matches[1], true);
            $this->assertIsArray($config);

            $this->assertSame(
                ['all', 'checking', 'approved', 'delivered', 'declined'],
                array_keys($config['tFilters'] ?? [])
            );
            $this->assertTrue($config['filtersEnabled']);
            $this->assertTrue($config['helpEnabled']);
            $this->assertSame('all', $config['shopButton']);
            $this->assertSame('https://billing.example', $config['billingUrl']);
        } finally {
            $this->forgetShopSettings();
        }
    }

    public function test_display_config_endpoint_forbidden_when_cabinet_disabled(): void
    {
        $this->get('/account/login/email'); // warm up UrlGenerator

        // No DB in tests: CabinetService::config() degrades to disabled.
        $response = $this->get(route('account.api.display'));
        $response->assertStatus(403);
    }

    public function test_first_visible_page_defaults_to_orders(): void
    {
        // No DB in tests: displayConfig() degrades to defaults (all pages on).
        $this->assertSame('orders', CabinetService::firstVisiblePage());
    }

    public function test_legacy_cabinet_urls_redirect_to_account(): void
    {
        $this->get('/account/login/email'); // warm up UrlGenerator

        $cases = [
            '/cabinet/orders' => '/account/orders',
            '/cabinet' => '/account',
            '/cabinet-login/email' => '/account/login/email',
            '/cabinet-login' => '/account/login/email',
        ];
        foreach ($cases as $old => $new) {
            $response = $this->get($old);
            $response->assertStatus(301);
            $this->assertStringEndsWith($new, (string) $response->headers->get('Location'));
        }
    }

    public function test_renamed_routes_generate_new_urls(): void
    {
        $this->assertStringEndsWith('/account/orders', route('account.page', ['page' => 'orders']));
        $this->assertStringEndsWith('/account/login/email', route('account.login', ['step' => 'email']));
        $this->assertStringEndsWith(
            '/admin/customer-personal-account',
            route('admin.customer_personal_account')
        );
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.save_customer_personal_account_info'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.test_customer_personal_account_connection'));
    }

    public function test_admin_cabinet_form_references_existing_routes(): void
    {
        $html = view('admin.cabinet.form', [
            'cabinet_enabled' => false,
            'cabinet_url' => 'https://api.true-help.com',
            'cabinet_secret' => '',
            'cabinet_display' => CabinetService::displayDefaults(),
        ])->render();

        $this->assertStringContainsString(route('admin.save_customer_personal_account_info'), $html);
        $this->assertStringContainsString(route('admin.test_customer_personal_account_connection'), $html);
    }

    public function test_login_page_shows_autologin_box_for_login_key(): void
    {
        $this->get('/account/login/email'); // warm up UrlGenerator

        $key = 'lkl_testkey1234.signature';
        $response = $this->get('/account/login/email?login_key=' . $key);
        $response->assertStatus(200);
        $html = $response->getContent();
        $this->assertStringContainsString('id="shop-autologin"', $html);
        $this->assertStringContainsString('data-login-key="' . $key . '"', $html);
    }

    public function test_login_page_ignores_malformed_login_key(): void
    {
        $this->get('/account/login/email'); // warm up UrlGenerator

        $response = $this->get('/account/login/email?login_key=not-a-link');
        $response->assertStatus(200);
        $this->assertStringNotContainsString('id="shop-autologin"', $response->getContent());
    }

    public function test_autologin_endpoint_forbidden_when_cabinet_disabled(): void
    {
        $this->get('/account/login/email'); // warm up UrlGenerator (sandbox APP_URL quirk)
        // No DB in tests: CabinetService::config() degrades to disabled.
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('account.autologin'), ['login_token' => 'lkl_abc.def']);

        $response->assertStatus(403);
    }

    public function test_cabinet_repeat_url_prefers_api_formed_link(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*/repeat-context' => \Illuminate\Support\Facades\Http::response([
                'data' => ['order' => ['id' => 9100, 'repeatUrl' => 'https://true-checkout.com/order/begin?products=X&auth=9001&is_cxserv=1']],
            ], 200),
        ]);

        $method = new \ReflectionMethod(\App\Http\Controllers\AccountController::class, 'cabinetRepeatUrl');
        $url = $method->invoke(null, 'test-token', '', 9100);

        $this->assertSame(
            'https://true-checkout.com/order/begin?products=X&auth=9001&is_cxserv=1',
            $url
        );
    }

    public function test_cabinet_repeat_url_null_without_api_link(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*/repeat-context' => \Illuminate\Support\Facades\Http::response([
                'data' => ['order' => ['id' => 9100]],
            ], 200),
        ]);

        $method = new \ReflectionMethod(\App\Http\Controllers\AccountController::class, 'cabinetRepeatUrl');

        $this->assertNull($method->invoke(null, 'test-token', '', 9100));
    }
}

class CabinetLoginLayoutFakeLanguage
{
    public static function GetAllLanuages(): array
    {
        return [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'de', 'name' => 'Deutsch'],
            ['code' => 'fr', 'name' => 'Française'],
        ];
    }
}
