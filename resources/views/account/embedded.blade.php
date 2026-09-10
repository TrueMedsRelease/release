@extends('account.login')

@section('cab_title', __('text.cabinet_login_title'))

@section('cab_content')
    <h1 class="login-content__title">{{ __('text.cabinet_login_title') }}</h1>

    <form class="login-content__form form" method="post" action="{{ route('account.embedded.start') }}" id="cab-embedded-form" novalidate>
        @csrf
        {{-- Шаг 1 как в customerportal Login.vue: только email + кнопка. Капчи здесь нет. --}}
        <div id="cab-step-email">
            <div class="form__rows">
                @if (session('cabinet_embedded_error'))
                    <div class="login-content__error" role="alert">{{ session('cabinet_embedded_error') }}</div>
                @endif
                <div class="login-content__error" role="alert" id="cab-email-error" hidden></div>
                <div class="form__row" id="cab-email-row">
                    <label class="form__label" for="cab-email">{{ __('text.cabinet_login_email') }}</label>
                    <div class="form__input">
                        <input id="cab-email" name="email" type="email" autocomplete="email"
                               class="input" value="{{ old('email', $email) }}" />
                    </div>
                </div>
            </div>

            <button class="form__button button button--dark" type="button" id="cab-continue">
                <span>{{ __('text.cabinet_login_submit') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </button>
            <p class="login-content__hint">{{ __('text.cabinet_login_hint') }}</p>
        </div>

        {{-- Шаг 2 как в customerportal Login.vue: только пазл-капча, без поля email.
             После успешной проверки форма уходит сама (onCaptchaVerified → submitEmail). --}}
        <div id="cab-step-captcha" hidden>
            <div class="login-content__captcha">
                <div class="captcha-container" id="cab-captcha">
                    <div class="captcha-header">
                        <div class="captcha-title">{{ __('text.cabinet_captcha_title') }}</div>
                    </div>
                    <div class="captcha-subtitle">{{ __('text.cabinet_captcha_subtitle') }}</div>

                    <div class="captcha-canvas-wrapper" id="cab-captcha-canvas">
                        <img id="cab-captcha-bg" class="captcha-background" alt="Captcha background" draggable="false" style="display: none;" />
                        <div id="cab-captcha-piece" class="puzzle-piece" style="display: none;"></div>
                    </div>

                    <div class="captcha-controls">
                        <button class="btn btn-secondary" type="button" id="cab-captcha-refresh" aria-label="{{ __('text.cabinet_captcha_refresh') }}">🔄</button>
                        <button class="btn btn-primary" type="button" id="cab-captcha-verify">{{ __('text.cabinet_captcha_verify') }}</button>
                    </div>
                    <div class="status-indicator" id="cab-captcha-status"></div>
                </div>
                <p class="login-content__hint" style="margin-top: 8px; font-size: 12px;">{{ __('text.cabinet_captcha_hint') }}</p>
            </div>

            <div class="otp-actions">
                <button type="button" class="link-btn" id="cab-change-email">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 8l9 6 9-6"/></svg>
                    {{ __('text.cabinet_change_email') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@push('cab_login_scripts')
@php
// Отдельной переменной: @json([...]) с вложенными route() внутри
// ломает парсер Blade-директив (несбалансированные скобки в PHP).
$cabEmbeddedConfig = [
    'captchaUrl' => route('account.api.captcha'),
    'captchaVerifyUrl' => route('account.api.captcha.verify'),
    'tInvalidEmail' => __('text.errors_wrong_email'),
    'tChecking' => __('text.cabinet_captcha_checking'),
    'tSuccess' => __('text.cabinet_captcha_success'),
    'tFailed' => __('text.cabinet_captcha_failed'),
    'tError' => __('text.cabinet_captcha_error'),
    'tLoadFailed' => __('text.cabinet_captcha_load_failed'),
];
@endphp
<script>
window.CabEmbeddedConfig = @json($cabEmbeddedConfig);
</script>
<script src="{{ asset('js/cabinet/embedded.js') }}" defer></script>
@endpush
