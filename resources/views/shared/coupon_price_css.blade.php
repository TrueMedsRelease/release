{{-- Styles for shared.coupon_price (also used by design_17's JS-rendered pack table). Printed once. --}}
@include('shared.coupon_tag', ['css' => true])
@once
<style>
            .coupon-price { display: block; margin: 3px 0 0; padding: 0; font-size: max(12px, .85em); font-style: normal; font-weight: 400; line-height: 1.3; color: inherit; white-space: normal; }
            .coupon-price__sum { margin-inline-start: 2px; font-weight: 700; white-space: nowrap; }
            .coupon-price__label { display: none; font-style: normal; }
            /* design_17 chat drawer: the price wrapper is a flex row, keep the coupon price under it. */
            .product__price-wrapper:has(> .coupon-price) { flex-wrap: wrap; }
            .product__price-wrapper > .coupon-price { flex-basis: 100%; margin-top: 0; }
</style>
@endonce
