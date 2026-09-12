{{--
    Shared: тело страницы "Referral program". Структура по референсу
    (customerportal Referral.vue): about-текст, QR + ссылка, купон.
    Подача — шеринг-панель + купон-тикет. Стили — в per-design CSS.
    Переменные: $cabp_referral.
--}}
<div class="cabp-about">
    <p>{{ __('text.cabinet_referral_about_1') }}</p>
    <p>{{ __('text.cabinet_referral_about_2') }}</p>
</div>

<div class="cabp-share">
    <div class="cabp-share__main">
        <div class="cabp-share__qr">
            <div class="cabp-qr" aria-hidden="true"><span>QR</span></div>
            <p class="cabp-share__caption">{{ __('text.cabinet_referral_qr') }}</p>
        </div>
        <div class="cabp-share__link">
            <p class="cabp-share__hint">{{ __('text.cabinet_referral_qr_hint') }}</p>
            <span class="cabp-share__label">{{ __('text.cabinet_referral_link') }}</span>
            <code class="cabp-share__code">{{ $cabp_referral['link'] }}</code>
            <span class="button cabp-action cabp-action--dark" title="{{ __('text.cabinet_referral_copy_link') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="8" y="8" width="12" height="12" rx="2.5" stroke="currentColor" stroke-width="1.8" />
                    <path d="M16 8V5.5A2.5 2.5 0 0 0 13.5 3h-8A2.5 2.5 0 0 0 3 5.5v8A2.5 2.5 0 0 0 5.5 16H8" stroke="currentColor" stroke-width="1.8" />
                </svg>
                <span>{{ __('text.cabinet_copy') }}</span>
            </span>
        </div>
    </div>
    <div class="cabp-ticket">
        <div class="cabp-ticket__info">
            <p class="cabp-ticket__label">{{ __('text.cabinet_referral_share_coupon') }}</p>
            <p class="cabp-ticket__code">{{ $cabp_referral['coupon'] }}</p>
        </div>
        <span class="button cabp-action cabp-action--dark" title="{{ __('text.cabinet_referral_copy_coupon') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="8" y="8" width="12" height="12" rx="2.5" stroke="currentColor" stroke-width="1.8" />
                <path d="M16 8V5.5A2.5 2.5 0 0 0 13.5 3h-8A2.5 2.5 0 0 0 3 5.5v8A2.5 2.5 0 0 0 5.5 16H8" stroke="currentColor" stroke-width="1.8" />
            </svg>
            <span>{{ __('text.cabinet_copy') }}</span>
        </span>
    </div>
</div>
