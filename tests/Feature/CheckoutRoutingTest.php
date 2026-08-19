<?php

namespace Tests\Feature;

use Tests\TestCase;

class CheckoutRoutingTest extends TestCase
{
    public function test_checkout_page_returns_redirect_when_cart_empty(): void
    {
        $response = $this->get(route('checkout.index'));

        $response->assertStatus(302);
    }

    public function test_checkout_content_returns_redirect_when_cart_empty(): void
    {
        $response = $this->get(route('checkout.content'));

        $response->assertStatus(302);
    }

    public function test_checkout_routes_exist(): void
    {
        $routes = [
            'checkout.index',
            'checkout.content',
            'checkout.insurance',
            'checkout.secret_package',
            'checkout.shipping',
            'checkout.country',
            'checkout.coupon',
            'checkout.gift_card',
            'checkout.bonus_card_info',
            'checkout.change_checkount_bonus',
            'checkout.order',
            'checkout.auth',
            'checkout.crypto_info',
            'checkout.validate_for_crypt',
            'checkout.data_for_crypt',
            'checkout.local_payment_info',
            'checkout.data_for_local_payment',
            'checkout.local_payment',
            'checkout.paypal',
            'checkout.sendSepa',
            'checkout.check_payment',
            'checkout.complete',
            'checkout.zelleData',
            'checkout.zelle',
            'checkout.bonus_card_process',
            'checkout.forget_bonuses',
            'checkout.gift_card_process',
            'checkout.validate_for_wallet',
            'checkout.wallet_process',
            'checkout.recalculation',
            'checkout.open_banking_process',
            'checkout.new_order',
            'checkout.send_checkout_phone_email',
            'checkout.validate_for_google',
            'checkout.validate_for_sepa',
            'checkout.send_google',
            'checkout.log_google',
        ];

        foreach ($routes as $route) {
            $this->assertTrue(route($route) !== null, "Route $route should exist");
        }
    }

    public function test_design_17_view_exist(): void
    {
        $this->assertTrue(
            view()->exists('design_17.checkout'),
            'View design_17.checkout should exist'
        );

        $this->assertTrue(
            view()->exists('design_17.ajax.checkout_content'),
            'View design_17.ajax.checkout_content should exist'
        );

        $this->assertTrue(
            view()->exists('design_17.layouts.main'),
            'View design_17.layouts.main should exist'
        );
    }
}
