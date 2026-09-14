<?php

namespace Tests\Unit;

use App\Services\CheckoutFormPrefill;
use PHPUnit\Framework\TestCase;

class CheckoutFormPrefillTest extends TestCase
{
    public function test_maps_shipping_to_billing_and_shipping_form_keys(): void
    {
        $form = CheckoutFormPrefill::fromShipping([
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'countryCode' => 'us',
            'state' => 'ca',
            'city' => 'Los Angeles',
            'address' => '1 Main St',
            'postalCode' => '90001',
        ], 'jane@example.com');

        $this->assertSame('jane@example.com', $form['email']);
        $this->assertSame('Jane', $form['firstname']);
        $this->assertSame('Doe', $form['lastname']);
        $this->assertSame('US', $form['billing_country']);
        $this->assertSame('US', $form['shipping_country']);
        $this->assertSame('US', $form['phone_code']);
        $this->assertSame('CA', $form['billing_state']);
        $this->assertSame('CA', $form['shipping_state']);
        $this->assertSame('Los Angeles', $form['billing_city']);
        $this->assertSame('Los Angeles', $form['shipping_city']);
        $this->assertSame('1 Main St', $form['billing_address']);
        $this->assertSame('1 Main St', $form['shipping_address']);
        $this->assertSame('90001', $form['billing_zip']);
        $this->assertSame('90001', $form['shipping_zip']);
    }

    public function test_empty_values_do_not_overwrite(): void
    {
        $form = CheckoutFormPrefill::fromShipping([
            'firstname' => '',
            'lastname' => '  ',
            'countryCode' => '',
            'city' => 'Berlin',
        ]);

        $this->assertArrayNotHasKey('email', $form);
        $this->assertArrayNotHasKey('firstname', $form);
        $this->assertArrayNotHasKey('lastname', $form);
        $this->assertArrayNotHasKey('billing_country', $form);
        $this->assertSame('Berlin', $form['billing_city']);
        $this->assertSame('Berlin', $form['shipping_city']);
        // Платёжные поля не трогаем вообще.
        $this->assertArrayNotHasKey('card_numb', $form);
    }

    public function test_invalid_email_is_skipped(): void
    {
        $form = CheckoutFormPrefill::fromShipping(['city' => 'Paris'], 'not-an-email');

        $this->assertArrayNotHasKey('email', $form);
        $this->assertSame('Paris', $form['billing_city']);
    }
}
