<?php

$ids = static fn ($value) => array_values(array_filter(array_map('intval', explode(',', $value ?? '')), static fn ($id) => $id > 0));

return [
    'enabled' => env('SEASONAL_UPSELL_ENABLED', '1'),
    'limit' => 4,
    'max_quantity' => 10,
    // Same endpoint and JSON request format as the shop's existing country cache.
    'endpoint' => env('SEASONAL_UPSELL_ENDPOINT', 'http://true-serv.net/checkout/order.php'),
    'as_form' => env('SEASONAL_UPSELL_AS_FORM', false),
    // Optional additional local ED blocklist. The central feed must curate non-ED products.
    'excluded_categories' => $ids(env('SEASONAL_UPSELL_ED_CATEGORIES')),
];
