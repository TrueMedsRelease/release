@extends('account.layout')

{{--
    Referral program 1:1 СЃ customerportal assets/Pages/Referral.vue.
    РљР»Р°СЃСЃС‹ Рё СЃС‚СЂСѓРєС‚СѓСЂР° вЂ” С‚Рµ Р¶Рµ (.page__referrer.referrer, .referrer__qr,
    .action-referrer, tippy copy 900РјСЃ, СЃРєРµР»РµС‚РѕРЅ).
--}}

@section('cab_title', __('text.cabinet_nav_referral'))

@section('cab_content')
<section class="page__referrer referrer" x-data="cabinetReferral()" x-cloak>
    <div class="referrer__container">
        <div class="referrer__top-row top-row">
            <h2 class="top-row__title title">{{ __('text.cabinet_nav_referral') }}</h2>
        </div>

        <div class="load-error" role="alert" x-show="failed">
            <svg class="load-error__icon" width="36" height="36" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-attention" /></svg>
            <p class="load-error__title">{{ __('text.cabinet_nav_referral') }}</p>
            <p class="load-error__text" x-text="failed"></p>
            <button type="button" class="load-error__button" @click="reload()">{{ __('text.cabinet_reload') }}</button>
        </div>

        <template x-if="!failed">
            <div>
                <div class="bonus_card__body" style="margin-bottom: 20px; line-height: 1.5;">
                    <p>{{ __('text.cabinet_referral_about_1') }}</p>
                    <p>{{ __('text.cabinet_referral_about_2') }}</p>
                </div>
                <div class="referrer__body">
                    <div class="referrer__slogan">
                        <p class="referrer__descr">{{ __('text.cabinet_referral_qr_hint') }}</p>
                    </div>

                    {{-- РЎРєРµР»РµС‚РѕРЅ (ReferralSkeleton 1:1) --}}
                    <div aria-hidden="true" x-show="loading">
                        <div class="referrer__qr">
                            <span class="skeleton skeleton-referrer__qr-image" style="width: 140px; height: 140px; border-radius: 12px; display: block;"></span>
                            <div class="referrer__action action-referrer">
                                <span class="skeleton skeleton-referrer__link" style="width: 280px; max-width: 280px; height: 14px; border-radius: 6px;"></span>
                                <span class="skeleton" style="width: 24px; height: 24px; border-radius: 6px;"></span>
                            </div>
                        </div>
                        <div class="referrer__share-link">
                            <span class="skeleton skeleton-referrer__label" style="width: 320px; height: 14px; border-radius: 6px; margin-bottom: 10px;"></span>
                            <div class="referrer__action action-referrer">
                                <span class="skeleton" style="width: 220px; height: 14px; border-radius: 6px;"></span>
                                <span class="skeleton" style="width: 24px; height: 24px; border-radius: 6px;"></span>
                            </div>
                        </div>
                    </div>

                    <template x-if="!loading">
                        <div>
                            <div class="referrer__qr">
                                <div class="referrer__image">
                                    <picture>
                                        <img x-bind:src="qrCode" width="140" height="140" alt="Referral QR" loading="lazy">
                                    </picture>
                                </div>
                                <div class="referrer__action action-referrer">
                                    <div class="action-referrer__data" data-tippy-data x-text="link"></div>
                                    <div class="action-referrer__icon tippy" data-tippy>
                                        <button type="button" class="action-referrer__button" aria-label="{{ __('text.cabinet_referral_copy_link') }}" @click="copyValue('link', link)">
                                            <svg width="24" height="24" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-copy" /></svg>
                                        </button>
                                        <div class="tippy__content" x-bind:class="{ active: copied === 'link' }" data-tippy-content>{{ __('text.cabinet_copied') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="referrer__share-link" x-show="coupon">
                                <p class="referrer__text-share">{{ __('text.cabinet_referral_share_coupon') }}</p>
                                <div class="referrer__action action-referrer">
                                    <div class="action-referrer__data" data-tippy-data x-text="coupon"></div>
                                    <div class="action-referrer__icon tippy" data-tippy>
                                        <button type="button" class="action-referrer__button" aria-label="{{ __('text.cabinet_referral_copy_coupon') }}" @click="copyValue('coupon', coupon)">
                                            <svg width="24" height="24" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-copy" /></svg>
                                        </button>
                                        <div class="tippy__content" x-bind:class="{ active: copied === 'coupon' }" data-tippy-content>{{ __('text.cabinet_copied') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</section>
@endsection

@push('cab_scripts')
@php
$cabinetConfig = [
    'referralUrl' => route('account.api.referral'),
    'tScopeForbidden' => __('text.cabinet_scope_forbidden'),
];
@endphp
<script>
window.CabinetCsrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
window.CabinetConfig = @json($cabinetConfig);
</script>
<script src="{{ asset('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset('js/cabinet/account.js') }}?v={{ filemtime(public_path('js/cabinet/account.js')) }}"></script>
@endpush

