{{--
    Coupon discount and the total with it, under the cart total.
    Uses the numbers Cart::update_cart_total() put in session('total').
--}}
@php $ccDiscount = (float) session('total.coupon_discount', 0); @endphp
@if ($ccDiscount > 0 && \App\Services\CouponService::active())
    @once
        <style>
            .coupon-cart { display: block; margin-top: 10px; padding: 10px 12px; border: 1px dashed #2e9e4f; border-radius: 10px; background: rgba(46, 158, 79, .07); font-size: 14px; font-weight: 400; line-height: 1.4; text-align: left; color: inherit; }
            .coupon-cart__row { display: flex; justify-content: space-between; gap: 12px; }
            .coupon-cart__row b { white-space: nowrap; }
            .coupon-cart__minus { color: #2e9e4f; white-space: nowrap; }
            .coupon-cart__note { margin-top: 4px; font-size: 12px; opacity: .75; }
        </style>
    @endonce
    <span class="coupon-cart">
        <span class="coupon-cart__row">
            <span>{{ __('text.coupon_cart_discount') }} (&minus;{{ \App\Services\CouponService::percentLabel() }}%)</span>
            <span class="coupon-cart__minus">&minus;{{ \App\Models\Currency::convert($ccDiscount) }}</span>
        </span>
        <span class="coupon-cart__row">
            <span>{{ __('text.coupon_cart_total') }}</span>
            <b>{{ \App\Models\Currency::SumInCurrency([
                \App\Models\Currency::convert(session('total.product_total', 0), true),
                \App\Models\Currency::convert(session('total.shipping_total', 0)),
                \App\Models\Currency::convert($ccDiscount * -1),
            ]) }}</b>
        </span>
        <span class="coupon-cart__note" style="display:block">{{ __('text.coupon_cart_note') }}</span>
    </span>
@endif
