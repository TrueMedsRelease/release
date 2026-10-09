{{--
    Live: "Referral program" — Alpine (account.js cabinetReferral).
    Variables: $design.
--}}
<link rel="stylesheet" href="{{ asset_ver('css/cabinet-v1.css') }}">
@php
$cabLiveReferralConfig = [
    'referralUrl' => route('account.api.referral'),
    'loginUrl' => route('account.login', ['step' => 'email']),
    'tScopeForbidden' => __('text.cabinet_scope_forbidden'),
];
@endphp
<script>
window.CabinetCsrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
window.CabinetConfig = @json($cabLiveReferralConfig);
window.CabinetMeUrl = @json(route('account.api.me'));
</script>

<div x-data="cabinetReferral()" x-cloak>
    <div class="cabp-loaderror" role="alert" x-show="failed">
        <p class="cabp-loaderror__title">{{ __('text.cabinet_nav_referral') }}</p>
        <p class="cabp-loaderror__text" x-text="failed"></p>
        <button type="button" class="button cabp-action cabp-action--dark" @click="reload()">{{ __('text.cabinet_reload') }}</button>
    </div>

    <template x-if="!failed">
        <div>
            <div class="cabp-about">
                <p>{{ __('text.cabinet_referral_about_1') }}</p>
                <p>{{ __('text.cabinet_referral_about_2') }}</p>
            </div>

            <div class="cabp-share">
                <div class="cabp-skel-list" aria-hidden="true" x-show="loading">
                    <span class="cabp-skel" style="width:60%"></span><br><br><span class="cabp-skel" style="width:80%"></span>
                </div>
                <template x-if="!loading">
                    <div>
                        <div class="cabp-share__main">
                            <div class="cabp-share__qr">
                                <img class="cabp-qr" x-bind:src="qrCode" width="140" height="140" alt="Referral QR" loading="lazy" x-show="qrCode">
                                <div class="cabp-qr" aria-hidden="true" x-show="!qrCode"><span>QR</span></div>
                                <p class="cabp-share__caption">{{ __('text.cabinet_referral_qr') }}</p>
                            </div>
                            <div class="cabp-share__link">
                                <p class="cabp-share__hint">{{ __('text.cabinet_referral_qr_hint') }}</p>
                                <span class="cabp-share__label">{{ __('text.cabinet_referral_link') }}</span>
                                <code class="cabp-share__code" x-text="link"></code>
                                <span class="button cabp-action cabp-action--dark" @click="copyValue('link', link)" style="cursor:pointer" title="{{ __('text.cabinet_referral_copy_link') }}">
                                    <span x-text="copied === 'link' ? '{{ __('text.cabinet_copied') }}' : '{{ __('text.cabinet_copy') }}'"></span>
                                </span>
                            </div>
                        </div>
                        <div class="cabp-ticket" x-show="coupon">
                            <div class="cabp-ticket__info">
                                <p class="cabp-ticket__label">{{ __('text.cabinet_referral_share_coupon') }}</p>
                                <p class="cabp-ticket__code" x-text="coupon"></p>
                            </div>
                            <span class="button cabp-action cabp-action--dark" @click="copyValue('coupon', coupon)" style="cursor:pointer" title="{{ __('text.cabinet_referral_copy_coupon') }}">
                                <span x-text="copied === 'coupon' ? '{{ __('text.cabinet_copied') }}' : '{{ __('text.cabinet_copy') }}'"></span>
                            </span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>
