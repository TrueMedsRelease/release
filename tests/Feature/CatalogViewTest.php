<?php

namespace Tests\Feature;

use Tests\TestCase;

class CatalogViewTest extends TestCase
{
    public function test_catalog_view_exists(): void
    {
        $this->assertTrue(
            view()->exists('design_17.catalog'),
            'View design_17.catalog should exist'
        );
    }

    public function test_catalog_cards_partial_exists(): void
    {
        $this->assertTrue(
            view()->exists('design_17.ajax.catalog_cards'),
            'View design_17.ajax.catalog_cards should exist'
        );
    }
}
