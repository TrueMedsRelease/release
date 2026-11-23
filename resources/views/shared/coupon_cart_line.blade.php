{{--
    Coupon row under the whole cart total (its own full-width row, right-aligned):
        Applied automatically at checkout  [−10%]  $55.99
    Included as the LAST CHILD of each design's totals container (not inside the price),
    so it never squeezes into the price layout. Numbers come from Cart::update_cart_total().
--}}
@php $ccDiscount = (float) session('total.coupon_discount', 0); @endphp
@if ($ccDiscount > 0 && \App\Services\CouponService::active())
    @php
        $ccTotal = \App\Models\Currency::SumInCurrency([
            \App\Models\Currency::convert(session('total.product_total', 0), true),
            \App\Models\Currency::convert(session('total.shipping_total', 0)),
            \App\Models\Currency::convert($ccDiscount * -1),
        ]);
        $ccTitle = __('text.coupon_cart_discount') . ': −' . \App\Models\Currency::convert($ccDiscount);
    @endphp
    @include('shared.coupon_tag', ['css' => true])
    @once
        <style>
            .coupon-cart {
                display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 4px 10px;
                flex: 0 0 100%; grid-column: 1 / -1; align-self: stretch;
                width: 100%; max-width: none; box-sizing: border-box;
                margin: 12px 0 0; padding: 12px 0 0;
                border-top: 1px dashed rgba(127, 127, 127, .35);
                font-size: 14px; font-style: normal; font-weight: 400; line-height: 1.3;
                color: inherit; text-align: end; text-transform: none; letter-spacing: normal;
            }
            .coupon-cart__note { font-size: 13px; opacity: .7; }
            .coupon-cart__deal { display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; }
            .coupon-cart__deal .coupon-tag { font-size: 12px; padding: 3px 7px 3px 6px; }
            .coupon-cart__sum { font-size: 18px; font-weight: 700; }
            /* the totals container keeps its own layout; it just lets our row wrap below */
            :where(*):has(> .coupon-cart) { flex-wrap: wrap; }
            @media (max-width: 600px) {
                .coupon-cart { justify-content: space-between; }
                .coupon-cart__note { font-size: 12px; }
            }
        </style>
    @endonce
    <div class="coupon-cart" title="{{ $ccTitle }}">
        <small class="coupon-cart__note">{{ __('text.coupon_cart_note') }}</small>
        <b class="coupon-cart__deal">@include('shared.coupon_tag')<b class="coupon-cart__sum">{{ $ccTotal }}</b></b>
    </div>
@endif
