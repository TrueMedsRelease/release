<?php

namespace Tests\Feature;

use App\Services\CabinetService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CabinetPreviewTest extends TestCase
{
    /**
     * Примечание: в песочнице APP_URL задан без схемы (testshop.test),
     * из-за чего ПЕРВЫЙ url() в свежем приложении собирает кривой URI
     * и первый HTTP-запрос уходит в fallback-404. Поэтому сначала делаем
     * прогревочный запрос. На проде с корректным APP_URL и реальным
     * веб-сервером (нормальный REQUEST_URI) этого эффекта нет.
     */
    public function test_live_redirects_to_design_login_when_no_token(): void
    {
        $this->get('/cabinet-login/email'); // прогреваем UrlGenerator
        $this->seedShopSettings();

        try {
            foreach (['orders', 'bonus', 'referral', 'contact', 'chat'] as $page) {
                $response = $this->get('/cabinet/' . $page);
                $response->assertRedirect(route('cabinet.login', ['step' => 'email']));
            }
        } finally {
            $this->forgetShopSettings();
        }
    }

    public function test_designless_login_renders(): void
    {
        $this->get('/cabinet-login/email'); // прогреваем UrlGenerator
        $this->seedShopSettings();

        try {
            foreach (['email', 'captcha', 'code'] as $step) {
                $response = $this->get('/cabinet-login/' . $step);
                $response->assertStatus(200);
                $response->assertSee('cablogin-', false);
            }
            // Пейджера превью больше нет, вместо муляжа языка — рабочий блок языка.
            $html = $this->get('/cabinet-login/email')->getContent();
            $this->assertStringNotContainsString('cablogin-pager', $html);
            $this->assertStringContainsString('cablogin-header__lang', $html);
        } finally {
            $this->forgetShopSettings();
        }
    }

    public function test_unknown_design_404_on_designless_routes(): void
    {
        $this->get('/cabinet-login/email'); // прогреваем UrlGenerator

        $response = $this->withSession(['design' => 'design_99'])->get('/cabinet/orders');
        $response->assertNotFound();

        $response = $this->withSession(['design' => 'design_99'])->get('/cabinet-login/email');
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
        $this->get('/cabinet-login/email'); // прогреваем UrlGenerator
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
        $this->get('/cabinet-login/email'); // прогреваем UrlGenerator
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
     * Настройки интеграции без сети: кеш обходит и БД, и кабинет.
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
     * У каждого дизайна с кабинетом есть живые и логин-вьюхи (без моков).
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
