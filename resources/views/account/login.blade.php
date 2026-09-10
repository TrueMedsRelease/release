<!DOCTYPE html>
{{--
    Экран входа — layout 1:1 с customerportal assets/Layouts/LoginLayout.vue:
    .wrapper.wrapper--login > .login > (.login__body > .login__inner >
    header.login-header + main.login-content + footer.login-footer)
    + .login__image. Стили — true-help + cabinet-app + cabinet-components.
--}}
@php
    $cabLocale = str_replace('_', '-', app()->getLocale());
    $cabPhones = [
        ['US', '+1-800-981-5864'], ['CA', '+1-647-370-8281'], ['AU', '+61-48-207-5486'],
        ['UK', '+44-20-3835-4834'], ['FR', '+33-64-463-9941'], ['ES', '+34-51-889-9156'],
        ['NZ', '+64-80-074-8315'],
    ];
    $cabLocales = [
        'en' => 'English', 'arb' => 'اللغة العربية الفصحى الحديثة', 'cs' => 'Čeština',
        'da' => 'Dansk', 'de' => 'Deutsch', 'es' => 'Español', 'fi' => 'Suomi',
        'fr' => 'Française', 'gr' => 'Ελληνική', 'hans' => '简体字', 'hant' => '繁體字',
        'hu' => 'Magyar nyelv', 'it' => 'Italiano', 'ja' => '日本語',
        'ms' => 'Bahasa Melayu', 'nl' => 'Nederlands', 'no' => 'Norsk',
        'pl' => 'Polszczyzna', 'pt' => 'Português', 'ro' => 'Limba română',
        'sk' => 'Slovenský', 'sv' => 'Svenska',
    ];
    $cabCurrentLocale = app()->getLocale();
@endphp
<html lang="{{ $cabLocale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('cab_title')</title>
    <link rel="icon" href="{{ asset('icon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('icon.svg') }}">
    <link rel="stylesheet" href="{{ asset('cabinet/true-help.css') }}">
    <link rel="stylesheet" href="{{ asset('cabinet/cabinet-app.css') }}">
    <link rel="stylesheet" href="{{ asset('cabinet/cabinet-components.css') }}">
    <link rel="stylesheet" href="{{ asset('cabinet/login.css') }}">
    <style>[x-cloak] { display: none !important; }</style>
    <script src="{{ asset('js/cabinet-alpine.min.js') }}" defer></script>
</head>
<body>
<div class="cab-login">
    <div class="wrapper wrapper--login" lang="{{ $cabLocale }}">
        <div class="login">
            <div class="login__body">
                <div class="login__inner">
                    <header class="login-header">
                        <a href="{{ route('account.index') }}" class="login-header__logo" aria-label="True Help login">
                            <img src="{{ asset('cabinet/logo-white.svg') }}" width="186" height="44" alt="True Help">
                        </a>
                        <div class="login-header__select">
                            @include('account.partials.lang-select')
                        </div>
                    </header>

                    <main class="login-content">
                        @yield('cab_content')
                    </main>

                    <footer class="login-footer">
                        <div class="login-footer__phones">
                            @foreach ($cabPhones as [$cabCountry, $cabPhone])
                                <a href="tel:{{ str_replace('-', '', $cabPhone) }}" class="login-footer__phone">{{ $cabCountry }}: {{ $cabPhone }}</a>
                            @endforeach
                        </div>
                    </footer>
                </div>
            </div>
            <div class="login__image">
                <picture>
                    <source srcset="{{ asset('cabinet/hero.webp') }}" type="image/webp">
                    <img src="{{ asset('cabinet/hero.png') }}" width="1388" height="1080" alt="True Help customer service">
                </picture>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/cabinet/custom-scrollbar.js') }}?v={{ filemtime(public_path('js/cabinet/custom-scrollbar.js')) }}"></script>
<script src="{{ asset('js/cabinet/lang-select.js') }}?v={{ filemtime(public_path('js/cabinet/lang-select.js')) }}"></script>
@stack('cab_login_scripts')
</body>
</html>
