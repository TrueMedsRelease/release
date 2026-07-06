@extends($design . '.layouts.main')

@section('title', __('text.checkout_title'))

@php
    if (!function_exists('asset_ver')) {
        function asset_ver(string $path): string {
            static $mtimes = [];
            $full = public_path($path);
            if (!isset($mtimes[$path])) {
                $mtimes[$path] = is_file($full) ? filemtime($full) : null;
            }
            $url = asset($path);
            $v = $mtimes[$path] ?? time();
            return $url . '?v=' . $v;
        }
    }
@endphp

@section('content')
<style>
    .checkout_wrapper { position: relative; min-height: 200px; }
    .checkout-preloader { position: absolute; inset: 0; z-index: 9; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.9); border-radius: 12px; }
    .checkout-preloader__spinner { width: 52px; height: 52px; border: 5px solid #e5e5e5; border-top-color: #14151a; border-radius: 50%; animation: cs-spin 0.8s linear infinite; }
    @keyframes cs-spin { to { transform: rotate(360deg); } }
    .checkout-ajax-loader { position: fixed; top: 16px; right: 16px; z-index: 9998; display: flex; align-items: center; gap: 8px; padding: 8px 14px; background: #fff; border-radius: 999px; box-shadow: 0 2px 12px rgba(0,0,0,0.12); font-size: 13px; color: #14151a; }
    .checkout-ajax-loader__spinner { width: 16px; height: 16px; border: 2px solid #e5e5e5; border-top-color: #14151a; border-radius: 50%; animation: cs-spin 0.8s linear infinite; display: inline-block; }
    .checkout-ajax-loader[hidden] { display: none; }
    .checkout-fatal { position: fixed; top: 16px; left: 50%; transform: translateX(-50%); z-index: 9997; max-width: 92%; padding: 14px 22px; background: #ed4c54; color: #fff; border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.18); font-size: 14px; text-align: center; }
    .checkout-fatal[hidden] { display: none; }
    .checkout_wrapper .poopuptext { display: none; }
    .checkout_wrapper .poopuptext.show { display: block; position: absolute; top: -1.8rem; right: 0; padding: 0.2rem 0.8rem; background: #C53030; color: #fff; font-size: 1rem; line-height: 1.4; border-radius: 0.6rem; white-space: nowrap; z-index: 1; }
    .checkout_wrapper .poopuptext.show::after { content: ''; position: absolute; bottom: -0.4rem; right: 1rem; width: 0; height: 0; border-left: 0.4rem solid transparent; border-right: 0.4rem solid transparent; border-top: 0.4rem solid #C53030; }
    .checkout-page .chat-scroll-down { display: none !important; }
    .checkout_wrapper .form__field.has-error .form__text-input,
    .checkout_wrapper .form__field.has-error select,
    .checkout_wrapper .form__field.has-error .select-wrapper { border-color: #C53030 !important; box-shadow: 0 0 0 1px #C53030 inset; border-radius: 6px; }

    #legacy-alert-container { position: fixed; bottom: 16px; right: 16px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; min-width: 280px; max-width: 400px; pointer-events: none; }
    #legacy-alert-container .legacy-alert { pointer-events: auto; }
    .legacy-alert { display: flex; align-items: flex-start; gap: 10px; padding: 14px 16px; border-radius: 12px; box-shadow: 0 4px 16px rgba(19,22,63,0.08); font-family: var(--font-inter, Inter, sans-serif); font-size: 1.3rem; line-height: 1.5; opacity: 0; transform: translateX(20px); transition: opacity 0.3s cubic-bezier(0.4,0.8,0.4,1), transform 0.3s cubic-bezier(0.4,0.8,0.4,1); }
    .legacy-alert.is-visible { opacity: 1; transform: translateX(0); }
    .legacy-alert.is-hiding { opacity: 0; transform: translateX(20px); }
    .legacy-alert--info { background: #fff; color: var(--color-primary, #13163f); border: 1px solid var(--color-gray-3, #d5d9de); }
    .legacy-alert--info .legacy-alert__icon { color: var(--color-primary, #13163f); }
    .legacy-alert--success { background: #fff; color: #262d38; border: 1px solid var(--color-green, #81c61c); }
    .legacy-alert--success .legacy-alert__icon { color: var(--color-green, #81c61c); }
    .legacy-alert--warning { background: #fff; color: #262d38; border: 1px solid #e5c84c; }
    .legacy-alert--warning .legacy-alert__icon { color: #b8960f; }
    .legacy-alert--error { background: #fff; color: #262d38; border: 1px solid var(--color-red, #e14c4c); }
    .legacy-alert--error .legacy-alert__icon { color: var(--color-red, #e14c4c); }
    .legacy-alert__icon { flex-shrink: 0; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 16px; margin-top: 1px; }
    .legacy-alert__text { flex: 1; min-width: 0; word-break: break-word; color: var(--color-text-2, #262d38); }
    .legacy-alert__close { flex-shrink: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border: none; background: var(--color-gray-1, #f0f5fa); color: var(--color-gray-4, #797c80); cursor: pointer; border-radius: 6px; font-size: 14px; line-height: 1; padding: 0; transition: background 0.2s cubic-bezier(0.4,0.8,0.4,1), color 0.2s; margin-top: 0; }
    .legacy-alert__close:hover { background: var(--color-gray-2, #e6ebf0); color: var(--color-primary, #13163f); }



</style>
<div class="checkout-ajax-loader" id="checkout-ajax-loader" hidden>
    <span class="checkout-ajax-loader__spinner"></span>
    <span class="checkout-ajax-loader__text">{{ __('text.checkout_order') }}</span>
</div>
<div class="checkout-fatal" id="checkout-fatal" hidden></div>
<div class="main__content">
    <div class="main__heading">
        <h1 class="h1">{{ __('text.checkout_order') }}</h1>
        <a class="button button--white button--return" href="{{ route('cart.index') }}">
            <span class="icon">
                <svg width="1em" height="1em" fill="currentColor">
                    <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#arrow-left") }}"></use>
                </svg>
            </span>
            {{ __('text.checkout_back') }}
        </a>
    </div>
    <div class="checkout_wrapper">
        <div class="checkout-preloader" id="checkout-preloader">
            <div class="checkout-preloader__spinner"></div>
        </div>
    </div>
    <script>
        window.checkoutRoutes = {
            content: {!! json_encode(route('checkout.content')) !!},
            insurance: {!! json_encode(route('checkout.insurance')) !!},
            secretPackage: {!! json_encode(route('checkout.secret_package')) !!},
            shipping: {!! json_encode(route('checkout.shipping')) !!},
            country: {!! json_encode(route('checkout.country')) !!},
            coupon: {!! json_encode(route('checkout.coupon')) !!},
            giftCard: {!! json_encode(route('checkout.gift_card')) !!},
            bonusCardInfo: {!! json_encode(route('checkout.bonus_card_info')) !!},
            changeBonus: {!! json_encode(route('checkout.change_checkount_bonus')) !!},
            forgetBonuses: {!! json_encode(route('checkout.forget_bonuses')) !!},
            auth: {!! json_encode(route('checkout.auth')) !!},
            order: {!! json_encode(route('checkout.order')) !!},
            paypal: {!! json_encode(route('checkout.paypal')) !!},
            sendSepa: {!! json_encode(route('checkout.sendSepa')) !!},
            localPaymentInfo: {!! json_encode(route('checkout.local_payment_info')) !!},
            dataForLocalPayment: {!! json_encode(route('checkout.data_for_local_payment')) !!},
            localPayment: {!! json_encode(route('checkout.local_payment')) !!},
            validateForCrypt: {!! json_encode(route('checkout.validate_for_crypt')) !!},
            dataForCrypt: {!! json_encode(route('checkout.data_for_crypt')) !!},
            cryptoInfo: {!! json_encode(route('checkout.crypto_info')) !!},
            checkPayment: {!! json_encode(route('checkout.check_payment')) !!},
            validateForGoogle: {!! json_encode(route('checkout.validate_for_google')) !!},
            validateForSepa: {!! json_encode(route('checkout.validate_for_sepa')) !!},
            validateForWallet: {!! json_encode(route('checkout.validate_for_wallet')) !!},
            walletProcess: {!! json_encode(route('checkout.wallet_process')) !!},
            sendGoogle: {!! json_encode(route('checkout.send_google')) !!},
            logGoogle: {!! json_encode(route('checkout.log_google')) !!},
            zelleData: {!! json_encode(route('checkout.zelleData')) !!},
            zelle: {!! json_encode(route('checkout.zelle')) !!},
            bonusCardProcess: {!! json_encode(route('checkout.bonus_card_process')) !!},
            giftCardProcess: {!! json_encode(route('checkout.gift_card_process')) !!},
            openBanking: {!! json_encode(route('checkout.open_banking_process')) !!},
            recalculation: {!! json_encode(route('checkout.recalculation')) !!},
            complete: {!! json_encode(route('checkout.complete')) !!},
            sendCheckoutPhoneEmail: {!! json_encode(route('checkout.send_checkout_phone_email')) !!}
        };
        window.checkoutConfig = {
            intlTelUtils: {!! json_encode(asset($design . '/vendor/intl-tel/js/utils.js')) !!},
            design: {!! json_encode($design) !!},
            selectorLocale: {
                placeholder: {!! json_encode(__('text.checkout_placeholder')) !!},
                search: {!! json_encode(__('text.checkout_search')) !!},
                clear: {!! json_encode(__('text.checkout_clear')) !!},
                remove: {!! json_encode(__('text.checkout_remove')) !!},
                noResults: {!! json_encode(__('text.checkout_no_results')) !!},
                loading: {!! json_encode(__('text.checkout_loading_data')) !!},
            }
        };
        window.checkoutTexts = {
            fatalError: {!! json_encode(__('text.checkout_error')) !!},
            loaderMessages: {
                insurance: {!! json_encode(__('text.loader_insurance')) !!},
                secretPackage: {!! json_encode(__('text.loader_secret_package')) !!},
                shipping: {!! json_encode(__('text.loader_shipping')) !!},
                country: {!! json_encode(__('text.loader_country')) !!},
                coupon: {!! json_encode(__('text.loader_coupon')) !!},
                giftCard: {!! json_encode(__('text.loader_gift_card')) !!},
                bonusCardInfo: {!! json_encode(__('text.loader_bonus_card')) !!},
                changeBonus: {!! json_encode(__('text.loader_bonus')) !!},
                forgetBonuses: {!! json_encode(__('text.loader_bonuses')) !!},
                auth: {!! json_encode(__('text.loader_auth')) !!},
                recalculation: {!! json_encode(__('text.loader_recalculation')) !!},
                localPaymentInfo: {!! json_encode(__('text.loader_payment_info')) !!},
                cryptoInfo: {!! json_encode(__('text.loader_crypto')) !!},
                walletProcess: {!! json_encode(__('text.loader_wallet')) !!},
                sendGoogle: {!! json_encode(__('text.loader_google_pay')) !!},
                sendSepa: {!! json_encode(__('text.loader_sepa')) !!},
                zelle: {!! json_encode(__('text.loader_zelle')) !!},
                openBanking: {!! json_encode(__('text.loader_open_banking')) !!},
                order: {!! json_encode(__('text.loader_order')) !!},
            },
            paymentErrorVisaMessage: {!! json_encode(__('text.payment_error_visa_message') ?? 'Visa is temporarily unavailable for this order') !!},
            paymentErrorRiskCheckMessage: {!! json_encode(__('text.payment_error_risk_check_message') ?? "Payment didn't pass security check. Please try another method.") !!},
            paymethodUnavailable: {!! json_encode(__('text.paymethod_unavailable') ?? 'Unfortunately, this payment method is currently unavailable') !!},
            paymethodRecommend: {!! json_encode(__('text.paymethod_recommend') ?? 'We recommend') !!},
            paymethodPayWith: {!! json_encode(__('text.paymethod_pay_with') ?? 'Pay with') !!},
            paymethodShowOther: {!! json_encode(__('text.paymethod_show_other') ?? 'Show other options') !!},
            paymethodTryDifferentCard: {!! json_encode(__('text.paymethod_try_different_card') ?? 'Try a different card') !!},
            paymethodCardDesc: {!! json_encode(__('text.paymethod_card_desc') ?? 'Select a card type. Previous card details will be cleared.') !!},
            paymethodSkipOther: {!! json_encode(__('text.paymethod_skip_other') ?? 'Skip, show other options') !!},
            paymethodExhaustedTitle: {!! json_encode(__('text.paymethod_exhausted_title') ?? 'Unfortunately, no payment methods worked') !!},
            paymethodExhaustedDesc: {!! json_encode(__('text.paymethod_exhausted_desc') ?? 'Please contact support to complete your order. Include this order ID:') !!},
            paymethodExhaustedClose: {!! json_encode(__('text.paymethod_exhausted_close') ?? 'Close') !!},
            paymethodBenefitExpress: {!! json_encode(__('text.paymethod_benefit_express') ?? 'Express checkout \u2014 pay with your saved cards in one tap') !!},
            paymethodBenefitSecureBiometric: {!! json_encode(__('text.paymethod_benefit_secure_biometric') ?? 'Secure \u2014 authenticated with Face ID, Touch ID, or fingerprint') !!},
            paymethodBenefitSimpleWallet: {!! json_encode(__('text.paymethod_benefit_simple_wallet') ?? 'Simple \u2014 no need to enter card number, expiry date, or CVV') !!},
            paymethodBenefitInstant: {!! json_encode(__('text.paymethod_benefit_instant') ?? 'Instant processing \u2014 your order is processed seconds after payment') !!},
            paymethodBenefitSecureBank: {!! json_encode(__('text.paymethod_benefit_secure_bank') ?? 'Secure \u2014 payment through your bank, no card details involved') !!},
            paymethodBenefitSimpleBank: {!! json_encode(__('text.paymethod_benefit_simple_bank') ?? 'Simple \u2014 no card number, expiry date, or CVV needed') !!},
            paymethodBenefitReliable: {!! json_encode(__('text.paymethod_benefit_reliable') ?? 'Reliable \u2014 direct transfer between banks') !!},
            paymethodBenefitTransparent: {!! json_encode(__('text.paymethod_benefit_transparent') ?? 'Transparent \u2014 all payment details will be shown') !!},
            paymethodBenefitUniversal: {!! json_encode(__('text.paymethod_benefit_universal') ?? 'Universal \u2014 works from any country') !!},
            paymethodBenefitInstantConfirm: {!! json_encode(__('text.paymethod_benefit_instant_confirm') ?? 'Instant transaction confirmation on the blockchain') !!},
        };
    </script>
    <link href="{{ asset_ver($design . '/js/custom-selector/CustomSelector.css') }}" rel="stylesheet">
    <script src="{{ asset_ver($design . '/vendor/floating-ui/core@1.6.9.min.js') }}"></script>
    <script src="{{ asset_ver($design . '/vendor/floating-ui/dom@1.6.13.min.js') }}"></script>
    <script type="module">
        import CustomSelector from '{{ asset($design . '/js/custom-selector/CustomSelector.js') }}';
        window.CustomSelector = CustomSelector;
        document.dispatchEvent(new CustomEvent('custom-selector-ready', { detail: { CustomSelector } }));
    </script>
    <script defer src="{{ asset_ver($design . '/js/checkout.js') }}"></script>
    <script defer src="{{ asset_ver($design . '/js/alert.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.Checkout17 && typeof window.Checkout17.loadCheckoutContent === 'function') {
                window.Checkout17.loadCheckoutContent();
            } else {
                console.error('[checkout17] module not loaded — checkout.js failed');
            }
        });
    </script>


</div>
@endsection
