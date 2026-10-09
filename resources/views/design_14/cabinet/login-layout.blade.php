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
    <link rel="stylesheet" href="{{ asset_ver($design . '/css/cabinet-login.css') }}">
</head>
<body>
<div class="cablogin">
    <div class="cablogin__body">
        <div class="cablogin__inner">
            <header class="cablogin-header">
                <a class="cablogin-header__logo" href="{{ route('account.login', ['step' => 'email']) }}" aria-label="TrueHelp Client login">
                    <img src="{{ asset('cabinet-login/logo-support.svg') }}" width="200" height="38" alt="TrueHelp Client — customer service system">
                </a>
                <span class="cablogin-header__lang">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.7 3.8 9S14.5 18.4 12 21c-2.5-2.6-3.8-5.7-3.8-9S9.5 5.6 12 3z"/></svg>
                    @php
                        try { $cabLangs = $Language::GetAllLanuages(); } catch (\Throwable $e) { $cabLangs = []; }
                        $cabCurrentLang = null;
                        foreach ($cabLangs as $cabLangItem) {
                            if (($cabLangItem['code'] ?? null) === App::currentLocale()) { $cabCurrentLang = $cabLangItem; break; }
                        }
                        if ($cabCurrentLang === null && count($cabLangs) > 0) { $cabCurrentLang = $cabLangs[0]; }
                    @endphp
                    @if (count($cabLangs) > 1)
                        <span class="cablogin-lang select" x-data="cabinetLangSelect()" :class="{ '_select-open': langOpen, '_drop-up': langOpen && dropUp }" @click.outside="close()" x-cloak>
                            <span class="select__body" x-ref="body">
                                <button type="button" class="cablogin-lang__title select__title" x-ref="title" @click="toggle()" @keydown="onTitleKeydown($event)" :aria-expanded="langOpen ? 'true' : 'false'" aria-haspopup="listbox" aria-label="Language">
                                    <span class="select__value">
                                        <span class="select__text">{{ $cabCurrentLang['name'] ?? App::currentLocale() }}</span>
                                        <span class="select__icon" aria-hidden="true">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                        </span>
                                    </span>
                                </button>
                                <span class="select__options" x-show="langOpen" x-ref="panel">
                                    <span class="select__scroll" x-ref="scroll" role="listbox" aria-label="Language">
                                        @foreach ($cabLangs as $cabLang)
                                            <a href="{{ route('home.language', $cabLang['code']) }}" class="select__option{{ (($cabLang['code'] ?? null) === App::currentLocale()) ? ' _select-selected' : '' }}" role="option" aria-selected="{{ (($cabLang['code'] ?? null) === App::currentLocale()) ? 'true' : 'false' }}" @keydown="onOptionKeydown($event, {{ $loop->index }}, {{ count($cabLangs) }})">{{ $cabLang['name'] }}</a>
                                        @endforeach
                                    </span>
                                </span>
                            </span>
                        </span>
                    @else
                        <span>{{ App::currentLocale() }}</span>
                    @endif
                </span>
            </header>

            <main class="cablogin-content">
                <h1 class="cablogin-content__title">{{ __('text.cabinet_login_title') }}</h1>
                @yield('cab_content')
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
    'autologinUrl' => route('account.autologin'),
    'captchaUrl' => route('account.login', ['step' => 'captcha']),
    'tInvalidEmail' => __('text.errors_wrong_email'),
    'tChecking' => __('text.cabinet_captcha_checking'),
    'tSuccess' => __('text.cabinet_captcha_success'),
    'tFailed' => __('text.cabinet_captcha_failed'),
    'tError' => __('text.cabinet_captcha_error'),
    'tLoadFailed' => __('text.cabinet_captcha_load_failed'),
    'tAutologinFailed' => __('text.cabinet_link_invalid'),
];
$shopCodeConfig = [
    'resend' => __('text.cabinet_resend'),
    'resendIn' => __('text.cabinet_resend_in'),
    'emailUrl' => route('account.login', ['step' => 'email']),
];
@endphp
<script>
window.ShopLoginConfig = @json($shopLoginConfig);
window.ShopCodeConfig = @json($shopCodeConfig);
</script>
<script src="{{ asset_ver('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset_ver('js/cabinet/custom-scrollbar.js') }}"></script>
<script src="{{ asset_ver('js/cabinet/lang-select.js') }}"></script>
<script src="{{ asset_ver('js/cabinet/device-lib.js') }}" defer></script>
<script src="{{ asset_ver('js/cabinet/shop-login.js') }}" defer></script>
</body>
</html>
