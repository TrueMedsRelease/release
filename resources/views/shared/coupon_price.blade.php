{{--
    Price with the coupon applied, under a package price on the product page.
    @include('shared.coupon_price', ['couponBase' => $item['price'], 'couponProductId' => $product['id']])
--}}
@php $cpDiscount = \App\Services\CouponService::discount((float) $couponBase, $couponProductId ?? null); @endphp
@if ($cpDiscount > 0)
    @once
        <style>
            .coupon-price { display: block; margin-top: 4px; font-size: 13px; font-weight: 400; line-height: 1.3; color: #2e9e4f; }
            .coupon-price b { font-weight: 700; white-space: nowrap; }
        </style>
    @endonce
    <span class="coupon-price">{{ __('text.coupon_price_label') }} &minus;{{ \App\Services\CouponService::percentLabel() }}%: <b>{{ \App\Models\Currency::convert($couponBase - $cpDiscount, true) }}</b></span>
@endif
