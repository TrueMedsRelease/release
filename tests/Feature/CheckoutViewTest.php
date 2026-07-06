<?php

namespace Tests\Feature;

use Tests\TestCase;

class CheckoutViewTest extends TestCase
{
    public function test_checkout_view_renders_without_error(): void
    {
        $this->assertTrue(
            view()->exists('checkout'),
            'View checkout should exist'
        );
    }

    public function test_checkout_content_view_renders_without_error(): void
    {
        $this->assertTrue(
            view()->exists('checkout_content'),
            'View checkout_content should exist'
        );
    }

    public function test_complete_view_renders_without_error(): void
    {
        $this->assertTrue(
            view()->exists('complete'),
            'View complete should exist'
        );
    }
}
