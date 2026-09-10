<!DOCTYPE html>
{{--
    Встроенный личный кабинет — layout 1:1 с customerportal:
    assets/Layouts/AppLayout.vue + assets/Components/AppHeader.vue
    + assets/Components/AppFooter.vue + assets/Components/MobileBottomBar.vue.

    Классы и структура — те же (.wrapper, .top-phones-header, .header,
    .menu-drawer, .page, .footer, .bottom-dock). Стили:
    /cabinet/true-help.css + /cabinet/cabinet-app.css + /cabinet/cabinet-components.css.
    Интерактив (drawer, popover, аккордеон, бейдж корзины) — Alpine (cabinetShell).
--}}
@php
    $cabLocale = str_replace('_', '-', app()->getLocale());
    $cabRoute = Route::currentRouteName();
    $cabNav = [
        ['route' => 'account.orders', 'label' => __('text.cabinet_nav_orders'), 'match' => ['account.orders'], 'scope' => 'orders'],
        ['route' => 'account.referral', 'label' => __('text.cabinet_nav_referral'), 'match' => ['account.referral'], 'scope' => 'referral'],
        ['route' => 'account.bonus', 'label' => __('text.cabinet_nav_bonus'), 'match' => ['account.bonus'], 'scope' => 'bonus'],
        ['url' => route('home.help'), 'label' => __('text.cabinet_nav_faq'), 'match' => [], 'scope' => null],
        ['route' => 'account.contact', 'label' => __('text.cabinet_nav_contact'), 'match' => ['account.contact'], 'scope' => 'contact'],
    ];
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
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('cab_title', __('text.cabinet_my_orders'))</title>
    <link rel="icon" href="{{ asset('icon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('icon.svg') }}">
    <link rel="stylesheet" href="{{ asset('cabinet/true-help.css') }}">
    <link rel="stylesheet" href="{{ asset('cabinet/cabinet-app.css') }}">
    <link rel="stylesheet" href="{{ asset('cabinet/cabinet-components.css') }}">
    {{-- Legacy-стили shop-кабинета: только для служебных страниц (start/sessions),
         скоуп .cab-account — портальную вёрстку не затрагивают. --}}
    <link rel="stylesheet" href="{{ asset('css/cabinet-account.css') }}">
    <style>
        [x-cloak] { display: none !important; }
        /* Нижнего дока в шопе нет (только кабинет) — FAB фильтров у нижнего края. */
        @media (max-width: 649px) {
            .oc-filters-fab { bottom: 20px; }
            .oc-filters-pop._open { bottom: 80px; }
        }
    </style>
</head>
<body>
<div class="wrapper" x-data="cabinetShell()" x-bind:class="{ 'menu-open': menuOpen }" lang="{{ $cabLocale }}">
    <a class="skip-link" href="#main-content">{{ __('text.cabinet_skip_to_content') }}</a>

    {{-- Полоска с телефонами — обычный поток, уезжает при скролле (AppHeader 1:1) --}}
    <div class="top-phones-header">
        <div class="header__container top-phones-header__items">
            @foreach ($cabPhones as [$cabCountry, $cabPhone])
                <a href="tel:{{ str_replace('-', '', $cabPhone) }}" class="top-phones-header__item"><b>{{ $cabCountry }}:</b> {{ $cabPhone }}</a>
            @endforeach
        </div>
    </div>

    {{-- Основной хедер — sticky (AppHeader 1:1) --}}
    <header class="header" x-bind:class="{ '_stuck': headerStuck }" x-ref="headerEl">
        <div class="header__container">
            <div class="header__content">
                <a href="{{ route('account.orders') }}" class="header__logo" aria-label="True Help orders">
                    <img src="{{ asset('cabinet/logo.svg') }}" width="186" height="44" alt="True Help">
                </a>

                <nav class="header__menu top menu" aria-label="Main navigation">
                    <ul class="menu__list">
                        @foreach ($cabNav as $cabItem)
                            {{-- Скоуп выключен у API-клиента (запомнили 403) — пункт прячем. --}}
                            @if (!empty($cabItem['scope']) && session('cabinet.forbidden.'.$cabItem['scope'])) @continue @endif
                            @php
                                $cabHref = $cabItem['url'] ?? route($cabItem['route']);
                                $cabActive = in_array($cabRoute, $cabItem['match'], true);
                            @endphp
                            <li class="menu__item">
                                <a href="{{ $cabHref }}" class="menu__link @if ($cabActive) _active @endif">{{ $cabItem['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <div class="header__actions">
                    <button type="button" class="app-shop-button" @click="window.location.href='{{ route('home.index') }}'">{{ __('text.cabinet_continue_shopping') }}</button>
                    <div class="header__select">
                        @include('account.partials.lang-select')
                    </div>

                    {{-- Компакт-юзер с поповером (AppHeader 1:1). Скрыт целиком,
                         если скоуп me выключен у API-клиента (403). --}}
                    <div class="user-compact" x-show="!meForbidden" @keydown.escape.window="userPopoverOpen = false">
                        <button type="button" class="user-compact__trigger" aria-label="User menu" x-bind:aria-expanded="String(userPopoverOpen)" @click.stop="userPopoverOpen = !userPopoverOpen">
                            <span class="user-compact__avatar" x-text="avatarInitials"></span>
                        </button>
                        <div class="user-popover" role="dialog" aria-label="User info" x-show="userPopoverOpen" x-cloak @click.outside="userPopoverOpen = false">
                            <div class="user-popover__head">
                                <span class="user-popover__avatar" x-text="avatarInitials"></span>
                                <div class="user-popover__meta">
                                    <span class="user-popover__name" x-text="userName"></span>
                                    <span class="user-popover__id" x-text="userIdLabel"></span>
                                </div>
                            </div>
                            <a href="{{ route('account.bonus') }}" class="user-popover__bonus">
                                <span>{{ __('text.cabinet_nav_bonus') }}</span>
                                <b x-text="bonusText"></b>
                            </a>
                            <div class="user-popover__hint">{{ __('text.cabinet_header_bonus_tooltip') }}</div>
                        </div>
                    </div>

                    <form method="post" action="{{ route('account.logout') }}" class="app-logout-form">
                        @csrf
                        <button type="submit" class="app-logout-button" aria-label="{{ __('text.cabinet_header_logout') }}">
                            <svg width="18" height="18" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-login" /></svg>
                        </button>
                    </form>

                    <button type="button" class="icon-menu header__burger" aria-controls="main-drawer" aria-label="{{ __('text.cabinet_header_menu') }}" x-bind:aria-expanded="String(menuOpen)" @click="menuOpen = !menuOpen">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Drawer-меню (AppHeader 1:1) --}}
        <div class="menu-backdrop" x-show="menuOpen" x-cloak @click="menuOpen = false"></div>
        <aside id="main-drawer" class="menu-drawer" x-bind:class="{ '_open': menuOpen }" role="dialog" aria-modal="true" aria-label="Main navigation" x-bind:aria-hidden="String(!menuOpen)" x-cloak>
            <div class="menu-drawer__head">
                <img src="{{ asset('cabinet/logo.svg') }}" width="140" height="33" alt="True Help">
                <button type="button" class="menu-drawer__close" aria-label="Close" @click="menuOpen = false">
                    <svg width="18" height="18" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-close" /></svg>
                </button>
            </div>

            <div class="menu-drawer__scroll" x-ref="drawerScroll">
                <nav class="menu-drawer__nav">
                    @foreach ($cabNav as $cabItem)
                        @if (!empty($cabItem['scope']) && session('cabinet.forbidden.'.$cabItem['scope'])) @continue @endif
                        @php
                            $cabHref = $cabItem['url'] ?? route($cabItem['route']);
                            $cabActive = in_array($cabRoute, $cabItem['match'], true);
                        @endphp
                        <a href="{{ $cabHref }}" class="menu-drawer__link @if ($cabActive) _active @endif" @click="menuOpen = false">{{ $cabItem['label'] }}</a>
                    @endforeach

                    <div class="menu-drawer__acc" x-bind:class="{ '_open': phonesOpen }">
                        <button type="button" class="menu-drawer__link menu-drawer__acc-toggle" x-bind:aria-expanded="String(phonesOpen)" @click="phonesOpen = !phonesOpen">
                            {{ __('text.cabinet_header_support_phones') }}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div class="menu-drawer__acc-panel" x-show="phonesOpen" x-cloak>
                            <div class="menu-drawer__acc-list" x-ref="phonesList">
                                @foreach ($cabPhones as [$cabCountry, $cabPhone])
                                    <a href="tel:{{ str_replace('-', '', $cabPhone) }}" class="menu-drawer__phone"><b>{{ $cabCountry }}</b> {{ $cabPhone }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            {{-- Язык отдельной строкой над карточкой юзера (AppHeader 1:1) --}}
            <div class="menu-drawer__lang">
                <div class="header__select menu-drawer__lang-select">
                    @include('account.partials.lang-select')
                </div>
            </div>

            <div class="menu-drawer__footer">
                <div class="menu-drawer__user-top" x-show="!meForbidden">
                    <span class="menu-drawer__avatar" x-text="avatarInitials"></span>
                    <div class="menu-drawer__user-meta">
                        <span class="menu-drawer__user-name" x-text="userName"></span>
                        <span class="menu-drawer__user-id" x-text="userIdLabel"></span>
                    </div>
                </div>
                <div class="menu-drawer__user-actions">
                    <a href="{{ route('account.bonus') }}" class="user-info-header__bonus menu-drawer__bonus" x-show="!meForbidden">
                        <span x-text="bonusText"></span>
                        <span class="user-info-header__icon tippy">
                            <svg width="16" height="16" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-question" /></svg>
                            <span class="tippy__content">{{ __('text.cabinet_header_bonus_tooltip') }}</span>
                        </span>
                    </a>
                    <form method="post" action="{{ route('account.logout') }}" class="app-logout-form">
                        @csrf
                        <button type="submit" class="app-logout-button" aria-label="{{ __('text.cabinet_header_logout') }}">
                            <svg width="18" height="18" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-login" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    </header>

    <main id="main-content" class="page">
        @yield('cab_content')
    </main>

    <footer class="footer">
        <div class="footer__container">
            <div class="footer__content">
                <a href="mailto:{{ __('text.cabinet_footer_email') }}" class="footer__link">{{ __('text.cabinet_footer_email') }}</a>
                <p class="footer__copyright">{{ __('text.cabinet_footer_copy') }}</p>
            </div>
        </div>
    </footer>

    {{-- Нижний док (MobileBottomBar 1:1, только ≤649px): меню + разделы кабинета.
         Внешние ссылки (FAQ) сюда не входят — они есть в drawer. --}}
    <nav class="bottom-dock" aria-label="{{ __('text.cabinet_header_menu') }}">
        <div class="bottom-dock__pill bottom-dock__pill--tabs">
            <button type="button" class="bottom-dock__tab" aria-label="{{ __('text.cabinet_header_menu') }}" @click="menuOpen = !menuOpen">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
                <span>{{ __('text.cabinet_header_menu') }}</span>
            </button>
            @foreach ($cabNav as $cabItem)
                @if (isset($cabItem['url'])) @continue @endif
                @if (!empty($cabItem['scope']) && session('cabinet.forbidden.'.$cabItem['scope'])) @continue @endif
                @php
                    $cabDockHref = route($cabItem['route']);
                    $cabDockActive = in_array($cabRoute, $cabItem['match'], true);
                @endphp
                <a href="{{ $cabDockHref }}" class="bottom-dock__tab @if ($cabDockActive) is-active @endif" @if ($cabDockActive) aria-current="page" @endif>
                    @if ($cabItem['route'] === 'account.orders')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3.5l7.5 3.8v9.4L12 20.5l-7.5-3.8V7.3L12 3.5z"/><path d="M4.5 7.3L12 11l7.5-3.7M12 11v9.5"/></svg>
                    @elseif ($cabItem['route'] === 'account.referral')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="M8.2 10.8l7.6-3.6M8.2 13.2l7.6 3.6"/></svg>
                    @elseif ($cabItem['route'] === 'account.bonus')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="8" width="17" height="4"/><path d="M12 8v12.5M6 12.5V20h12v-7.5"/><path d="M12 8s-1.5-4.5-4.2-4.5S5.5 8 12 8zm0 0s1.5-4.5 4.2-4.5S18.5 8 12 8z"/></svg>
                    @elseif ($cabItem['route'] === 'account.contact')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
                    @endif
                    <span>{{ $cabItem['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    {{-- Плавающая кнопка «Continue shopping» — только ≤649px, над доком слева. --}}
    <button type="button" class="app-shop-fab" @click="window.location.href='{{ route('home.index') }}'">{{ __('text.cabinet_continue_shopping') }}</button>

</div>
<script>
window.CabinetFpUrl = @json($cabinet_fp ?? '');
window.CabinetMeUrl = @json(route('account.api.me'));
</script>
{{-- Классические скрипты (без defer): выполняются при разборе, строго раньше
     отложенного Alpine — x-data-функции гарантированно определены к старту. --}}
<script src="{{ asset('js/cabinet/custom-scrollbar.js') }}?v={{ filemtime(public_path('js/cabinet/custom-scrollbar.js')) }}"></script>
<script src="{{ asset('js/cabinet/lang-select.js') }}?v={{ filemtime(public_path('js/cabinet/lang-select.js')) }}"></script>
@stack('cab_scripts')
</body>
</html>
