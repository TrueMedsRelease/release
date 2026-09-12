<?php

namespace Tests\Feature;

use App\Services\CabinetService;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class CabinetPreviewTest extends TestCase
{
    /**
     * Превью кабинета в лейауте магазина: без auth, данные — моки.
     *
     * Примечание: в песочнице APP_URL задан без схемы (testshop.test),
     * из-за чего ПЕРВЫЙ url() в свежем приложении собирает кривой URI
     * и первый HTTP-запрос уходит в fallback-404. Поэтому сначала делаем
     * прогревочный запрос. На проде с корректным APP_URL и реальным
     * веб-сервером (нормальный REQUEST_URI) этого эффекта нет.
     */
    public function test_preview_renders(): void
    {
        $this->get('/preview/cabinet/design_10/orders'); // прогреваем UrlGenerator

        $urls = [
            '/preview/cabinet/design_10/orders',
            '/preview/cabinet/design_10/bonus',
            '/preview/cabinet/design_10/referral',
            '/preview/cabinet/design_10/contact',
            '/preview/cabinet/design_10/chat',
            '/preview/cabinet-login/design_10/email',
            '/preview/cabinet-login/design_10/captcha',
            '/preview/cabinet-login/design_10/code',
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
            // Маркер превью-верстки в ответе.
            $marker = str_contains($url, 'cabinet-login') ? 'cablogin-' : 'cabp-';
            $response->assertSee($marker, false);
        }
    }

    public function test_layout_wraps_cabinet_content(): void
    {
        $this->get('/preview/cabinet/design_10/orders'); // прогрев UrlGenerator

        $response = $this->get('/preview/cabinet/design_10/orders');
        $response->assertStatus(200);
        // хром лейаута design_10 (логотип + обёртка страниц магазина)
        $response->assertSee('design_10/svg/logo.svg', false);
        $response->assertSee('page-wrapper', false);
    }

    public function test_orders_matches_reference_details(): void
    {
        $this->get('/preview/cabinet/design_10/orders'); // прогрев UrlGenerator

        $html = $this->get('/preview/cabinet/design_10/orders')->getContent();
        // пилюли по маппингу референса
        $this->assertStringContainsString('oc-pill--delivered', $html);
        $this->assertStringContainsString('oc-pill--processing', $html);
        $this->assertStringContainsString('oc-pill--pending', $html);
        // трекинг только в дровере и только у заказа с tracking_number
        $this->assertSame(1, substr_count($html, 'oc-track__panel'));
        $this->assertStringContainsString('RN123456789NL', $html);
        // billing-ссылка и shop-ссылка из референса
        $this->assertStringContainsString('billing.example.com/pay/104017', $html);
        $this->assertStringContainsString('cabp-confirm__dialog', $html);
        // sessions удалён
        $this->assertStringNotContainsString('sessions', $html);
    }

    public function test_unknown_design_404(): void
    {
        $controller = $this->app->make(\App\Http\Controllers\CabinetPreviewController::class);

        $this->expectException(NotFoundHttpException::class);
        $controller->page('design_99', 'orders');
    }

    public function test_live_redirects_to_design_login_when_embedded_on(): void
    {
        $this->get('/preview/cabinet/design_10/orders'); // прогреваем UrlGenerator
        $this->seedShopSettings(true);

        try {
            foreach (['orders', 'bonus', 'referral', 'contact', 'chat'] as $page) {
                $response = $this->get('/cabinet/' . $page);
                $response->assertRedirect(route('cabinet.login', ['step' => 'email']));
            }
        } finally {
            $this->forgetShopSettings();
        }
    }

    public function test_live_redirects_to_external_when_embedded_off(): void
    {
        $this->get('/preview/cabinet/design_10/orders'); // прогреваем UrlGenerator
        $this->seedShopSettings(false);

        try {
            foreach (['orders', 'bonus', 'referral', 'contact', 'chat'] as $page) {
                $response = $this->get('/cabinet/' . $page);
                $response->assertRedirect(route('account.start'));
            }
        } finally {
            $this->forgetShopSettings();
        }
    }

    public function test_designless_login_renders_when_embedded_on(): void
    {
        $this->get('/preview/cabinet/design_10/orders'); // прогреваем UrlGenerator
        $this->seedShopSettings(true);

        try {
            foreach (['email', 'captcha', 'code'] as $step) {
                $response = $this->get('/cabinet-login/' . $step);
                $response->assertStatus(200);
                $response->assertSee('cablogin-', false);
            }
        } finally {
            $this->forgetShopSettings();
        }
    }

    public function test_designless_login_redirects_to_external_when_embedded_off(): void
    {
        $this->get('/preview/cabinet/design_10/orders'); // прогреваем UrlGenerator
        $this->seedShopSettings(false);

        try {
            foreach (['email', 'captcha', 'code'] as $step) {
                $response = $this->get('/cabinet-login/' . $step);
                $response->assertRedirect(route('account.start'));
            }
        } finally {
            $this->forgetShopSettings();
        }
    }

    public function test_unknown_design_404_on_designless_routes(): void
    {
        $this->get('/preview/cabinet/design_10/orders'); // прогреваем UrlGenerator

        $response = $this->withSession(['design' => 'design_99'])->get('/cabinet/orders');
        $response->assertNotFound();

        $response = $this->withSession(['design' => 'design_99'])->get('/cabinet-login/email');
        $response->assertNotFound();
    }

    /**
     * Настройки интеграции без сети: кеш обходит и БД, и кабинет.
     */
    private function seedShopSettings(bool $embedded): void
    {
        Cache::put(CabinetService::SETTINGS_CACHE_KEY, ['shop_id' => 'test', 'embedded_login' => $embedded], 60);
    }

    private function forgetShopSettings(): void
    {
        Cache::forget(CabinetService::SETTINGS_CACHE_KEY);
    }

    /**
     * У каждого дизайна с кабинетом есть все 7 blade (без тяжёлого рендера).
     */
    public function test_all_designs_have_cabinet_views(): void
    {
        $designs = ['design_1', 'design_2', 'design_3', 'design_4', 'design_5', 'design_9', 'design_10', 'design_11', 'design_12', 'design_14', 'design_15', 'design_16', 'design_17'];
        $views = ['_nav', 'orders', 'bonus', 'referral', 'contact', 'chat', 'login-layout', 'login-email', 'login-captcha', 'login-code', 'live-orders', 'live-bonus', 'live-referral', 'live-contact', 'live-chat'];

        foreach ($designs as $design) {
            foreach ($views as $view) {
                $this->assertTrue(
                    view()->exists($design . '.cabinet.' . $view),
                    "View {$design}.cabinet.{$view} should exist"
                );
            }
        }

        foreach (['nav', 'orders-body', 'bonus-body', 'referral-body', 'contact-side', 'login-email-body', 'login-captcha-body', 'logincode-body'] as $partial) {
            $this->assertTrue(view()->exists('cabinet-parts.' . $partial), "Shared partial cabinet-parts.{$partial} should exist");
        }

        foreach (['nav-live', 'orders-live', 'bonus-live', 'referral-live', 'contact-live', 'chat-live'] as $partial) {
            $this->assertTrue(view()->exists('cabinet-live.' . $partial), "Shared partial cabinet-live.{$partial} should exist");
        }
    }
}
