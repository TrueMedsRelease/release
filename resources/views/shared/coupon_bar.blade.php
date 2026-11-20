{{--
    Thin bar on top of every storefront page while a validated coupon is active.
    Set APP_COUPON_NOTICE_ON=0 in .env to hide it.
--}}
@if (env('APP_COUPON_NOTICE_ON', 1) && \App\Services\CouponService::active())
<div class="coupon-bar" role="status">
    <svg class="coupon-bar__icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M3 9V6a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v3a3 3 0 0 0 0 6v3a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-3a3 3 0 0 0 0-6z"/><path d="M9 15l6-6"/>
    </svg>
    <span><b>{{ __('text.coupon_bar_title') }} &minus;{{ \App\Services\CouponService::percentLabel() }}%.</b> {{ __('text.coupon_bar_text') }}</span>
</div>
<style>
    .coupon-bar { position: relative; z-index: 50; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 16px; background: #2e9e4f; color: #fff; font-size: 14px; line-height: 1.35; text-align: center; }
    .coupon-bar b { font-weight: 700; }
    .coupon-bar__icon { flex: none; }
    @media (max-width: 600px) { .coupon-bar { font-size: 13px; padding: 7px 12px; } }
</style>
@endif
