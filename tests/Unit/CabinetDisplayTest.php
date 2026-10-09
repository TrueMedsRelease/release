<?php

namespace Tests\Unit;

use App\Http\Controllers\AccountController;
use App\Services\CabinetService;
use PHPUnit\Framework\TestCase;

class CabinetDisplayTest extends TestCase
{
    public function test_display_defaults(): void
    {
        $defaults = CabinetService::displayDefaults();

        $this->assertSame(
            ['orders' => true, 'bonus' => true, 'referral' => true, 'contact' => true, 'chat' => true],
            $defaults['pages']
        );
        $this->assertTrue($defaults['orders_filters']);
        $this->assertTrue($defaults['orders_help']);
        $this->assertSame('all', $defaults['orders_shop_button']);
        $this->assertSame('', $defaults['shop_domain_override']);
        $this->assertSame('', $defaults['billing_url_override']);
        $this->assertTrue($defaults['billing_button']);
    }

    public function test_normalize_display_config_empty_gives_defaults(): void
    {
        $this->assertSame(CabinetService::displayDefaults(), CabinetService::normalizeDisplayConfig([]));
    }

    public function test_normalize_display_config_flags_and_pages(): void
    {
        $config = CabinetService::normalizeDisplayConfig([
            'pages' => ['orders' => '1', 'bonus' => '0', 'chat' => 0, 'unknown_page' => '1'],
            'orders_filters' => '0',
            'orders_help' => 0,
            'billing_button' => 'yes',
            'unknown_key' => 'x',
        ]);

        $this->assertTrue($config['pages']['orders']);
        $this->assertFalse($config['pages']['bonus']);
        $this->assertFalse($config['pages']['chat']);
        $this->assertTrue($config['pages']['referral']);
        $this->assertArrayNotHasKey('unknown_page', $config['pages']);
        $this->assertFalse($config['orders_filters']);
        $this->assertFalse($config['orders_help']);
        $this->assertTrue($config['billing_button']);
        $this->assertArrayNotHasKey('unknown_key', $config);
    }

    public function test_normalize_display_config_shop_button_modes(): void
    {
        $this->assertSame('foreign', CabinetService::normalizeDisplayConfig(['orders_shop_button' => 'FOREIGN'])['orders_shop_button']);
        $this->assertSame('hide', CabinetService::normalizeDisplayConfig(['orders_shop_button' => 'hide'])['orders_shop_button']);
        $this->assertSame('all', CabinetService::normalizeDisplayConfig(['orders_shop_button' => 'everywhere'])['orders_shop_button']);
        $this->assertSame('all', CabinetService::normalizeDisplayConfig([])['orders_shop_button']);
    }

    public function test_normalize_display_config_urls(): void
    {
        $config = CabinetService::normalizeDisplayConfig([
            'shop_domain_override' => 'https://example.com?aff=1842',
            'billing_url_override' => 'not a url at all ///',
        ]);

        $this->assertSame('https://example.com?aff=1842', $config['shop_domain_override']);
        $this->assertSame('', $config['billing_url_override']);

        $bare = CabinetService::normalizeDisplayConfig(['shop_domain_override' => 'example.com']);
        $this->assertSame('https://example.com', $bare['shop_domain_override']);
    }

    public function test_apply_domain_override_empty_inputs(): void
    {
        $this->assertSame('', CabinetService::applyDomainOverride('', 'https://example.com'));
        $this->assertSame('https://shop1.example/x', CabinetService::applyDomainOverride('https://shop1.example/x', ''));
        $this->assertSame('https://shop1.example/x', CabinetService::applyDomainOverride('https://shop1.example/x', '   '));
    }

    public function test_apply_domain_override_host_swap_keeps_params(): void
    {
        $this->assertSame(
            'https://example.com?aff=5',
            CabinetService::applyDomainOverride('https://shop1.example?aff=5', 'https://example.com?aff=1842')
        );
    }

    public function test_apply_domain_override_appends_missing_params(): void
    {
        $this->assertSame(
            'https://example.com/catalog?aff=1842',
            CabinetService::applyDomainOverride('https://shop1.example/catalog', 'https://example.com?aff=1842')
        );
    }

    public function test_apply_domain_override_path_from_override(): void
    {
        $this->assertSame(
            'https://example.com/shop?a=1&aff=1842',
            CabinetService::applyDomainOverride('https://shop1.example/catalog?a=1', 'https://example.com/shop?aff=1842')
        );
    }

    public function test_apply_domain_override_relative_link(): void
    {
        $this->assertSame(
            'https://example.com/catalog?x=1&aff=1842',
            CabinetService::applyDomainOverride('catalog?x=1', 'https://example.com?aff=1842')
        );
    }

    public function test_apply_domain_override_invalid_override_keeps_link(): void
    {
        $this->assertSame(
            'https://shop1.example/x',
            CabinetService::applyDomainOverride('https://shop1.example/x', 'http://')
        );
    }

    public function test_allowed_order_status_has_no_refunded(): void
    {
        $this->assertSame('approved', AccountController::allowedOrderStatus('approved'));
        $this->assertSame('checking', AccountController::allowedOrderStatus('checking'));
        $this->assertSame('delivered', AccountController::allowedOrderStatus('delivered'));
        $this->assertSame('declined', AccountController::allowedOrderStatus('declined'));
        $this->assertNull(AccountController::allowedOrderStatus('refunded'));
        $this->assertNull(AccountController::allowedOrderStatus(''));
        $this->assertNull(AccountController::allowedOrderStatus('APPROVED'));
        $this->assertNull(AccountController::allowedOrderStatus('unknown'));
    }

    public function test_rewrite_order_shop_links_explicit_override(): void
    {
        $body = [
            'data' => ['items' => [
                ['id' => 1, 'shop' => 'https://shop1.example?aff=5'],
                ['id' => 2, 'shop' => null],
                ['id' => 3],
            ]],
        ];

        $result = AccountController::rewriteOrderShopLinks($body, 'https://example.com?aff=1842');

        $this->assertSame('https://example.com?aff=5', $result['data']['items'][0]['shop']);
        $this->assertNull($result['data']['items'][1]['shop']);
        $this->assertArrayNotHasKey('shop', $result['data']['items'][2]);
    }

    public function test_rewrite_order_shop_links_empty_override_keeps_body(): void
    {
        $body = ['data' => ['items' => [['id' => 1, 'shop' => 'https://shop1.example?aff=5']]]];

        $this->assertSame($body, AccountController::rewriteOrderShopLinks($body, ''));
    }
}
