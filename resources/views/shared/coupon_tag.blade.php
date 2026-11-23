{{--
    Small green "−10%" coupon tag (same ticket icon as the top coupon bar).
    Used by shared.coupon_cart_line and shared.coupon_price; inline, phrasing content only.
    @include('shared.coupon_tag', ['css' => true]) prints its styles (once per page), call it before the wrapper.
--}}
@if ($css ?? false)
    @include('shared.coupon_color')
    @once
    <style>
        .coupon-tag { display: inline-flex; align-items: center; gap: 3px; vertical-align: middle; margin: 0; padding: 2px 6px 2px 5px; border: 0; border-radius: 4px; background: #2e9e4f; background: var(--coupon-bg, color-mix(in srgb, var(--coupon-color, #2e9e4f) 82%, #000)); color: var(--coupon-on, #fff); font-family: inherit; font-size: 11px; font-style: normal; font-weight: 700; line-height: 1.2; letter-spacing: 0; text-transform: none; text-decoration: none; white-space: nowrap; }
        .coupon-tag svg { flex: none; display: block; width: 12px; height: 12px; margin: 0; fill: none; stroke: currentColor; }
        .coupon-tag svg path { fill: none; stroke: currentColor; }
    </style>
    @endonce
@else
<i class="coupon-tag" dir="ltr"><svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" role="img" aria-label="{{ __('text.coupon_price_label') }}"><path d="M3 9V6a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v3a3 3 0 0 0 0 6v3a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-3a3 3 0 0 0 0-6z"/><path d="M9 15l6-6"/></svg>&minus;{{ \App\Services\CouponService::percentLabel() }}%</i>
@endif
