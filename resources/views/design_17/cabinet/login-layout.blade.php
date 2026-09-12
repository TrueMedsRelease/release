{{--
    Отдельный layout логина кабинета (без chrome магазина).
    Структура 1:1 с референсом (customerportal LoginLayout.vue):
    сплит-скрин — панель формы (логотип customer service + язык + контент + телефоны)
    и hero-фото. Стили — design_10/css/cabinet-login.css.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('cab_title', __('text.cabinet_login_title'))</title>
    <link rel="stylesheet" href="{{ asset($design . '/css/cabinet-login.css') }}">
</head>
<body>
<div class="cablogin">
    <div class="cablogin__body">
        <div class="cablogin__inner">
            <header class="cablogin-header">
                <a class="cablogin-header__logo" href="{{ route('cabinet.login', ['step' => 'email']) }}" aria-label="TrueHelp Client login">
                    <img src="{{ asset('cabinet-login/logo-support.svg') }}" width="200" height="38" alt="TrueHelp Client — customer service system">
                </a>
                <span class="cablogin-header__lang">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.7 3.8 9S14.5 18.4 12 21c-2.5-2.6-3.8-5.7-3.8-9S9.5 5.6 12 3z"/></svg>
                    <span>English</span>
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2.5 4.5L6 8l3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </header>

            <main class="cablogin-content">
                <h1 class="cablogin-content__title">{{ __('text.cabinet_login_title') }}</h1>
                @yield('cab_content')
                <nav class="cablogin-pager" aria-label="Preview steps">
                    <span>Preview:</span>
                    <a href="{{ route('preview.cabinet.login', ['design' => $design, 'step' => 'email']) }}" @if (($cabp_step ?? '') === 'email') aria-current="page" @endif>1 · Email</a>
                    <a href="{{ route('preview.cabinet.login', ['design' => $design, 'step' => 'captcha']) }}" @if (($cabp_step ?? '') === 'captcha') aria-current="page" @endif>2 · Captcha</a>
                    <a href="{{ route('preview.cabinet.login', ['design' => $design, 'step' => 'code']) }}" @if (($cabp_step ?? '') === 'code') aria-current="page" @endif>3 · Code</a>
                </nav>
            </main>

            <footer class="cablogin-footer">
                <div class="cablogin-footer__phones">
                    <a href="tel:+18009815864">US: +1-800-981-5864</a>
                    <a href="tel:+16473708281">CA: +1-647-370-8281</a>
                    <a href="tel:+61482075486">AU: +61-48-207-5486</a>
                    <a href="tel:+442038354834">UK: +44-20-3835-4834</a>
                    <a href="tel:+33644639941">FR: +33-64-463-9941</a>
                    <a href="tel:+34518899156">ES: +34-51-889-9156</a>
                    <a href="tel:+64800748315">NZ: +64-80-074-8315</a>
                </div>
            </footer>
        </div>
    </div>
    <div class="cablogin__image" aria-hidden="true">
        <img src="{{ asset('cabinet-login/hero.png') }}" width="1388" height="1080" alt="" fetchpriority="high">
    </div>
</div>
@php
$shopLoginConfig = [
    'captchaImageUrl' => route('account.api.captcha'),
    'captchaVerifyUrl' => route('account.api.captcha.verify'),
    'captchaUrl' => route('cabinet.login', ['step' => 'captcha']),
    'tInvalidEmail' => __('text.errors_wrong_email'),
    'tChecking' => __('text.cabinet_captcha_checking'),
    'tSuccess' => __('text.cabinet_captcha_success'),
    'tFailed' => __('text.cabinet_captcha_failed'),
    'tError' => __('text.cabinet_captcha_error'),
    'tLoadFailed' => __('text.cabinet_captcha_load_failed'),
];
$shopCodeConfig = [
    'resend' => __('text.cabinet_resend'),
    'resendIn' => __('text.cabinet_resend_in'),
    'emailUrl' => route('cabinet.login', ['step' => 'email']),
];
@endphp
<script>
window.ShopLoginConfig = @json($shopLoginConfig);
window.ShopCodeConfig = @json($shopCodeConfig);
</script>
<script src="{{ asset('js/cabinet/device-lib.js') }}" defer></script>
<script src="{{ asset('js/cabinet/shop-login.js') }}" defer></script>
</body>
</html>
