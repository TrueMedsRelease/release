<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>@yield('title', 'Title')</title>
        <meta name="description" content="@yield('description', 'Description')">
        <meta name="keywords" content="@yield('keywords', 'Keywords')">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#14151a" />

        @foreach ($Language::GetAllLanuages() as $item)
            <link rel="alternate" href="{{ route('home.language', $item['code']) }}"
                @if ($item['code'] == 'arb')
                    hreflang="ar"
                @elseif ($item['code'] == 'gr')
                    hreflang="el"
                @elseif ($item['code'] == 'hans')
                    hreflang="zh-Hans"
                @elseif ($item['code'] == 'hant')
                    hreflang="zh-Hant"
                @else
                    hreflang={{ $item['code'] }}
                @endif
            />
        @endforeach

        <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.ico') }}" sizes="any">
        <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">

        @if (env('APP_PWA', 0))
            <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
            <script defer type="text/javascript" src="{{ asset("js/sw-setup.js") }}"></script>
        @endif

        {{-- <script type="text/javascript" src="{{ asset("js/delete_cache.js") }}"></script> --}}

        {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

        <link href="{{ asset($design . '/fonts/dm-sans-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/dm-sans-medium.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/dm-sans-bold.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/futura-pt-demi.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/futura-pt-book.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/marcellus-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/marcellus-sc-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/neue-machina-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/plus-jakarta-sans-medium.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">

        <link href="{{ asset($design . '/vendor/custom-select/custom-select.min.css') }}" rel="stylesheet">
        <link href="{{ asset($design . '/vendor/intl-tel/css/intlTelInput.min.css') }}" rel="stylesheet">
        <link href="{{ asset($design . '/css/style.css') }}" rel="stylesheet">
        <link href="{{ asset($design . '/css/pages.css') }}" rel="stylesheet">

        <script defer src="{{ asset('vendor/jquery/jquery-3.6.3.min.js') }}"></script>
        <script defer src="{{ asset('vendor/jquery/autocomplete.js') }}"></script>
        <script defer src="{{ asset('vendor/jquery/init.js') }}"></script>
        <script defer type="text/javascript" src="{{ asset('js/jquery-migrate-1.2.1.min.js') }}"></script>

        <script defer src="{{ asset($design . '/vendor/custom-select/custom-select.min.js') }}"></script>
        <script defer src="{{ asset($design . '/vendor/intl-tel/js/intlTelInput.min.js') }}"></script>
        <script defer src="{{ asset($design . '/vendor/just-validate.min.js') }}"> </script>
        {!! isset($pixel) ? $pixel : '' !!}
    </head>

    <body class="webp @yield('page_name')">
        <script>
            let flagc = false;
            let flagp = false;
            const design = 12;
        </script>

        @if (session('locale'))
            <input type="hidden" id="lang_session" value="{{ session('locale') }}">
        @endif

        @if (session('order'))
            <input type="hidden" id="order_info_session" value="{{ json_encode(session('order')) }}">
        @endif

        <input type="hidden" id="is_pwa_here" value="{{ env('APP_PWA', 0) }}">
        <input type="hidden" id="vapid_pub" value="{{ base64_encode(env('VAPID_PUBLIC_KEY', '')) }}">
        <input type="hidden" id="subsc_popup" value="{{ env('SUBSCRIBE_POPUP_STATUS', 1) }}">
        <input type="hidden" id="country_iso" value="{{ $codes }}">
        <input type="hidden" id="initial_country" value="{{ strtolower(session('location.country')) }}">

        @php
            $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
        @endphp

        @php
            $phone_arr = [
                1 => 'US',
                2 => 'CA',
                3 => 'AU',
                4 => 'UK',
                5 => 'FR',
                6 => 'ES',
                7 => 'NZ',
                8 => 'DK',
                9 => 'SE',
                10 => 'CH',
                11 => 'CZ',
                12 => 'FI',
                13 => 'GR',
                14 => 'PT',
                15 => 'DE',
                16 => 'IT',
                17 => 'NL'
            ];

            $country_code = session('location.country', 'US');

            if ($country_code && in_array($country_code, $phone_arr)) {
                $target_key = array_search($country_code, $phone_arr);
                $target_value = $phone_arr[$target_key];
                unset($phone_arr[$target_key]);

                $phone_arr = [$target_key => $target_value] + $phone_arr;
            }
        @endphp

        {{-- <div class="christmas" style="display: none">
            <img loading="lazy" src="{{ asset("pub_images/pay_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/christmas_big.png") }}">
        </div> --}}

        <div class="topbar">
            <div class="container">
                <div class="header-phones drag-nav">
                    <div class="drag-nav-container">
                        <div class="request-callback">
                            <button class="link" data-dialog="call">{{ __('text.common_callback') }}</button>
                            <span>&nbsp;{{ __('text.common_call_us_top') }}</span>
                        </div>
                        @foreach ($phone_arr as $id_phone => $phones)
                            <a href="tel:{{ __('text.phones_title_phone_' . $id_phone) }}">{{ __('text.phones_title_phone_' . $id_phone . '_code') }} {{ __('text.phones_title_phone_' . $id_phone) }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <header class="header">
            <div class="container">
                <nav class="nav cat-nav">
                    <div class="nav-container">
                        <div class="nav__heading">{{ __('text.common_categories_menu') }}</div>
                        <button class="nav__close-button" aria-label="Close categories"></button>
                        <ul class="nav__list">
                            <li class="nav__item">
                                <a class="nav__link nav__sublist-toggler" href="{{ route('home.index') }}" data-sublist-index="0">{{ __('text.common_best_selling_title') }}
                                    <span class="icon">
                                        <svg width="1em" height="1em"  fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-rr-angle-small-down') }}"></use>
                                        </svg>
                                    </span>
                                </a>
                            </li>
                            @foreach ($menu as $category)
                                <li class="nav__item">
                                    <a class="nav__link nav__sublist-toggler" href="{{ route('home.category', $category['url']) }}" data-sublist-index="{{ $loop->iteration }}">{{ $category['name'] }}
                                        <span class="icon">
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-rr-angle-small-down') }}"></use>
                                            </svg>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="categories-sublists">
                            <ul class="nav__sublist grid-sublist-4-col" data-sublist-index="0">
                                <li class="nav__item nav__item--return">
                                    <button class="nav__mobile-return">
                                        <span class="icon">
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                            </svg>
                                        </span>
                                        {{ __('text.common_best_selling_title') }}
                                    </button>
                                </li>
                                @foreach ($bestsellers as $bestseller)
                                    <li class="nav__item">
                                        <a class="nav__link" href="{{ route('home.product', $bestseller['url']) }}">
                                            {{ $bestseller['name'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            @foreach ($menu as $category)
                                <ul class="nav__sublist grid-sublist-4-col" data-sublist-index="{{ $loop->iteration }}">
                                    <li class="nav__item nav__item--return">
                                        <button class="nav__mobile-return">
                                            <span class="icon">
                                                <svg width="1em" height="1em" fill="currentColor">
                                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                                </svg>
                                            </span>
                                            {{ $category['name'] }}
                                        </button>
                                    </li>
                                    @foreach ($category['products'] as $item)
                                        <li class="nav__item">
                                            <a class="nav__link" href="{{ route('home.product', $item['url']) }}">{{ $item['name'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        </div>
                    </div>
                </nav>
                <div class="header-controls">
                    <a class="header__logo" href="{{ route('home.index') }}">
                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                            <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="{{ $domainWithoutZone }}">
                        @else
                            <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="Logo">
                        @endif
                    </a>
                    <div class="header-settings">
                        @if (count($Language::GetAllLanuages()) > 1)
                            <div class="header-lang header-select-wrapper">
                                <select class="header-select" id="lang_select" onchange="location.href=this.options[this.selectedIndex].value">
                                    @foreach ($Language::GetAllLanuages() as $item)
                                        <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}" @if (App::currentLocale() == $item['code']) selected @endif>{{ $item['name'] }}</option>
                                    @endforeach
                                </select>
                                <span class="icon header-select-wrapper__icon">
                                    <img src="{{ asset($design . '/images/icons/planet.svg') }}">
                                </span>
                                <span class="icon header-select-wrapper__chevron">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-rr-angle-small-down') }}"></use>
                                    </svg>
                                </span>
                            </div>
                        @endif
                        @if (count($Currency::GetAllCurrency()) > 1)
                            <div class="header-currency header-select-wrapper">
                                <select class="header-select" id="curr_select" onchange="location.href=this.options[this.selectedIndex].value">
                                    @foreach ($Currency::GetAllCurrency() as $item)
                                        <option value="{{ route('home.currency', $item['code']) }}" @if (session('currency') == $item['code']) selected @endif>{{ Str::upper($item['code']) }}</option>
                                    @endforeach
                                </select>
                                <span class="icon header-select-wrapper__icon">
                                    <img src="{{ asset($design . '/images/icons/wallet.svg') }}">
                                </span>
                                <span class="icon header-select-wrapper__chevron">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-rr-angle-small-down') }}"></use>
                                    </svg>
                                </span>
                            </div>
                        @endif
                        <a class="header-auth header-nav-link" href="{{ route('home.login') }}" target="_blank">
                            <span class="icon icon--grad">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#profile-circle-2') }}"></use>
                                </svg>
                            </span>
                            {{ __('text.common_profile') }}
                        </a>
                    </div>
                    <div class="header-caption">
                        <span class="icon icon--grad">
                            <svg width="1em" height="1em" fill="currentColor" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' verified pharmacy' }}" @endif>
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi_4223827') }}"></use>
                            </svg>
                        </span>
                        <div class="header-caption__title">{{ __('text.common_verified') }}</div>
                        <div class="header-caption__text">{{ __('text.common_approved_d4') }}</div>
                    </div>
                    <div class="header-brands">
                        <div class="header-brand">
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/brands/brand-01-49w.webp 1x, ' . $design . '/images/brands/brand-01-98w.webp 2x') }}">
                                <img src="{{ asset($design . '/images/brands/brand-01-49w.png') }}" srcset="{{ asset($design . '/images/brands/brand-01-49w.png 1x, ' . $design . '/images/brands/brand-01-98w.png 2x') }}" width="49" height="32" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fda' }}" @else alt="Brand" @endif>
                            </picture>
                        </div>
                        <div class="header-brand">
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/brands/brand-02-46w.webp 1x, ' . $design . '/images/brands/brand-02-93w.webp 2x') }}">
                                <img src="{{ asset($design . '/images/brands/brand-02-46w.png') }}" srcset="{{ asset($design . '/images/brands/brand-02-46w.png 1x, ' . $design . '/images/brands/brand-02-93w.png 2x') }}" width="47" height="36" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' pgue' }}" @else alt="Brand" @endif>
                            </picture>
                        </div>
                        <div class="header-brand">
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/brands/brand-03-45w.webp 1x, ' . $design . '/images/brands/brand-03-91w.webp 2x') }}">
                                <img src="{{ asset($design . '/images/brands/brand-03-45w.png') }}" srcset="{{ asset($design . '/images/brands/brand-03-45w.png 1x, ' . $design . '/images/brands/brand-03-91w.png 2x') }}" width="46" height="28" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @else alt="Brand" @endif>
                            </picture>
                        </div>
                        <div class="header-brand">
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/brands/brand-04-69w.webp 1x, ' . $design . '/images/brands/brand-04-139w.webp 2x') }}">
                                <img src="{{ asset($design . '/images/brands/brand-04-69w.png') }}" srcset="{{ asset($design . '/images/brands/brand-04-69w.png 1x, ' . $design . '/images/brands/brand-04-139w.png 2x') }}" width="70" height="36" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cipa' }}" @else alt="Brand" @endif>
                            </picture>
                        </div>
                        <div class="header-brand">
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/brands/brand-05-56w.webp 1x, ' . $design . '/images/brands/brand-05-113w.webp 2x') }}">
                                <img src="{{ asset($design . '/images/brands/brand-05-56w.png') }}" srcset="{{ asset($design . '/images/brands/brand-05-56w.png 1x, ' . $design . '/images/brands/brand-05-113w.png 2x') }}" width="57" height="32" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @else alt="Brand" @endif>
                            </picture>
                        </div>
                        <div class="header-brand">
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/brands/brand-06-72w.webp 1x, ' . $design . '/images/brands/brand-06-145w.webp 2x') }}">
                                <img src="{{ asset($design . '/images/brands/brand-06-72w.png') }}" srcset="{{ asset($design . '/images/brands/brand-06-72w.png 1x, ' . $design . '/images/brands/brand-06-145w.png 2x') }}" width="73" height="30" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mcafree' }}" @else alt="Brand" @endif>
                            </picture>
                        </div>
                    </div>

                    @php
                        $cart_count = 0;
                        $cart_total = 0;
                        if (!empty(session('cart'))) {
                            foreach (session('cart') as $value) {
                                $cart_count += $value['q'];
                                $cart_total += $value['price'] * $value['q'];
                            }
                        }
                    @endphp

                    <a class="cart-button button button--outlined" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                        <span class="icon icon--grad">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#shopping_cart') }}"></use>
                            </svg>
                        </span>
                        <span class="cart-button__text">{{ __('text.common_cart_text_d2') }}</span>
                        <span class="cart-button__total">{{ $Currency::Convert($cart_total, true) }}</span>
                    </a>
                    <div class="header-controls__nav-row">
                        <button class="button categories-button">
                            <span class="icon button__fries-icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#menu-fries') }}"></use>
                                </svg>
                            </span>
                            <span class="icon button__close-icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#close-thin') }}"></use>
                                </svg>
                            </span>
                            <span class="button__text">{{ __('text.common_categories_menu') }}</span>
                        </button>
                        <div class="header-nav greedy-nav">
                            <div class="greedy-items">
                                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a> </div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a></div>
                                @else
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a> </div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a></div>
                                @endif
                            </div>
                            <div class="dropdown" data-fixed-dropdown>
                                <button class="dropdown-toggler link greedy-button" aria-label="Show dropdown">
                                    <span class="icon">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-sr-menu-dots-vertical') }}"></use>
                                        </svg>
                                    </span>
                                    <span class="icon is-hidden">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#close') }}"></use>
                                        </svg>
                                    </span>
                                </button>
                                <div class="dropdown-container">
                                    <div class="dropdown-list greedy-hidden-items"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-info">
                <div class="container">
                    <div class="header-promo">
                        <div class="header-promo__title">
                            <span class="icon icon--grad">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#star') }}"></use>
                                </svg>
                            </span>
                            <span class="header-promo__title-text">1 000 000</span>
                        </div>
                        <div class="header-promo__text">{{ __('text.common_customers') }}</div>
                    </div>
                    <div class="header-items">
                        <div class="header-item">
                            <div class="header-item__title">
                                <span class="icon icon--grad">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi_1685179') }}"></use>
                                    </svg>
                                </span>{{ __('text.common_save') }}
                            </div>
                            <div class="header-item__text">{{ __('text.common_discount') }}</div>
                        </div>
                        <div class="header-item">
                            <div class="header-item__title">
                                <span class="icon icon--grad">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi_15566232') }}"></use>
                                    </svg>
                                </span>{{ __('text.common_prescription') }}
                            </div>
                            <div class="header-item__text">{{ __('text.common_restrictions') }}</div>
                        </div>
                        <div class="header-item">
                            <div class="header-item__title">
                                <span class="icon icon--grad">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi_9356319') }}"></use>
                                    </svg>
                                </span>{{ __('text.common_delivery') }}
                            </div>
                            <div class="header-item__text">{{ __('text.common_receive') }}</div>
                        </div>
                        <div class="header-item">
                            <div class="header-item__title">
                                <span class="icon icon--grad">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi_4544406') }}"></use>
                                    </svg>
                                </span>{{ __('text.common_moneyback') }}
                            </div>
                            <div class="header-item__text">{{ __('text.common_refund') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-bottom-row">
                <div class="container">
                    <form class="search-form" action="{{ route('search.search_product') }}" method="post">
                        @csrf
                        <div class="search search-bar" style="width: 100%">
                            <label class="search-form__label">
                                <input class="search-form__input input-text ac_input" id="autocomplete" type="text" placeholder="{{ __('text.common_search') }}" name="search_text" required>
                            </label>
                            <button class="search-form__button" aria-label="Search"></button>
                        </div>
                    </form>

                    <div class="dropdown index-dropdown">
                        <button class="dropdown-toggler index-button" aria-label="Show dropdown">
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#candy-box') }}"></use>
                                </svg>
                            </span>
                            <span class="icon is-hidden">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#close') }}"></use>
                                </svg>
                            </span>
                        </button>
                        <div class="dropdown-container">
                            <ul class="drug-index">
                                @foreach ($first_letters as $key => $active_letter)
                                    <li class="drug-index__item">
                                        @if ($active_letter)
                                            <div class="drug-index__link">
                                                <a href="{{ route('home.first_letter', $key) }}">{{ $key }}</a>
                                            </div>
                                        @else
                                            <div class="drug-index__link">
                                                {{ $key }}
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="promos">
            <div class="container">

                @yield('promo_bonus')

                <div class="promos-payment-methods drag-nav">
                    <div class="drag-nav-container">
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#visa') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#mastercard') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#maestro') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#discover') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#amex') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#jcb') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' union-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#union-pay') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#dinners-club') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#apple-pay') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#google-pay') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#amazon-pay') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#stripe') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#paypal') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#sepa') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#cashapp') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#adyen') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#skrill') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#worldpay') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#payline') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#bitcoin') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#binance-coin') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#ethereum') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#litecoin') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#tron') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#usdt(erc20)') }}">
                            </svg>
                        </div>
                        <div class="promos-payment-method">
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#usdt(trc20)') }}">
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="christmas" style="display: none" onclick="location.href='{{ route('home.checkup') }}'">
            <img loading="lazy" src="{{ asset("pub_images/checkup_img/black/checkup_big.png") }}">
        </div>

        @yield('content')

        <div class="sup-footer">
            <div class="subscribe">
                <div class="container">
                    <div class="subscribe__caption">
                        <div class="subscribe__title">{{ __('text.common_subscribe') }}</div>
                        <div class="subscribe__text">{{ __('text.common_spec_offer') }}</div>
                    </div>
                    <form class="subscribe-form">
                        <label class="subscribe-form__label">
                            <input class="subscribe-form__input" type="email" name="subscribe-email" placeholder="{{ __('text.affiliate_email') }}" required>
                        </label>
                        <button class="subscribe-form__button button button_sub" type="button">
                            <img src="{{ asset($design . '/images/icons/subscribe_mini.svg') }}" class="sub_mini">
                            <div class="sub_text">
                                {{ __('text.common_subscribe') }}
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            @yield('rewies')
        </div>

        <footer class="footer">
            <div class="container">
                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                    <div class="footer__wrapper">
                        <a class="footer__logo" href="{{ route('home.index') }}">
                            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="{{ $domainWithoutZone }}">
                            @else
                                <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="Logo">
                            @endif
                        </a>
                        <nav class="nav footer-nav">
                            <div class="nav-container">
                                <ul class="nav__list">
                                    <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></li>
                                    <li class="nav__item"><a class="nav__link" href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a></li>
                                    <li class="nav__item"><a class="nav__link" href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                                    <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a></li>
                                    <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a></li>
                                    <li class="nav__item"><a class="nav__link" href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a></li>
                                    <li class="nav__item"><a class="nav__link" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a></li>
                                </ul>
                            </div>
                        </nav>
                        <a class="footer__affiliate-button button" href="{{ route('home.affiliate', '_' . $domainWithoutZone) }}">{{ __('text.common_affiliate_main_menu_button') }}</a>
                    </div>
                @else
                    <div class="footer__wrapper">
                        <a class="footer__logo" href="{{ route('home.index') }}">
                            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="{{ $domainWithoutZone }}">
                            @else
                                <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="Logo">
                            @endif
                        </a>
                        <nav class="nav footer-nav">
                            <div class="nav-container">
                                <ul class="nav__list">
                                    <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></li>
                                    <li class="nav__item"><a class="nav__link" href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a></li>
                                    <li class="nav__item"><a class="nav__link" href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                                    <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a></li>
                                    <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a></li>
                                    <li class="nav__item"><a class="nav__link" href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a></li>
                                    <li class="nav__item"><a class="nav__link" href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a></li>
                                </ul>
                            </div>
                        </nav>
                        <a class="footer__affiliate-button button" href="{{ route('home.affiliate', '') }}">{{ __('text.common_affiliate_main_menu_button') }}</a>
                    </div>
                @endif

                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                    <div class="sitemap_menu">
                        <a class="nav__link" href="{{ route('home.sitemap', '_' . $domainWithoutZone) }}">{{__('text.menu_title_sitemap')}}</a>
                    </div>
                @endif

                <div class="footer__copyrights vw-container">
                    {{ __('text.license_text_license1_1') }}
                    {{ $domain }}
                    {{ __('text.license_text_license1_2') }}
                    {{ __('text.license_text_license2_d11') }}
                </div>
                <div class="footer-buttons">
                    <div class="footer-buttons__container">
                        <a class="footer-button" href="{{ route('home.index') }}">
                            <span class="icon icon--grad">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#home') }}"></use>
                                </svg>
                            </span>
                            <span class="button__text">{{ __('text.common_home_main_menu_item') }}</span>
                        </a>
                        <button class="footer-button footer-button--cat">
                            <span class="icon button__fries-icon icon--grad">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fries') }}"></use>
                                </svg>
                            </span>
                            <span class="icon button__close-icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#close-thin') }}"></use>
                                </svg>
                            </span>
                            <span class="button__text">{{ __('text.common_categories_menu') }}</span>
                        </button>
                        <a class="footer-button" href="{{ route('home.login') }}" target="_blank">
                            <span class="icon icon--grad">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#profile-circle-2') }}"></use>
                                </svg>
                            </span>
                            <span class="button__text">{{ __('text.common_profile') }}</span>
                        </a>
                        <a class="footer-button footer-button--cart" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                            <span class="icon icon--grad">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#shopping_cart') }}"></use>
                                </svg>
                            </span>
                            <span class="button__text">{{ $Currency::Convert($cart_total, true) }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </footer>

        <div class="cat-overlay overlay"></div>

        <dialog class="dialog-container" data-name="call" data-modal="true" data-clickable-backdrop="true">
            <div class="dialog">
                <header class="dialog__header">
                    <div class="dialog__title">{{ __('text.common_callback') }}</div>
                </header>
                <form class="form callback-form" method="dialog">
                    <div class="form__field text-field">
                        <input class="form__text-input input-tel intl-phone" type="tel" id="callback-phone" name="callback-phone" placeholder="000 000 00 00" required>
                        <label class="form__label label-tel" for="callback-phone"></label>
                    </div>
                    <div class="form__field submit-field">
                        <button class="button form__submit button_request_call" type="submit">{{ __('text.common_callback') }}</button>
                    </div>
                </form>
                <button class="dialog__close-button close-button">Close</button>
                <div class="message_sended hidden">
                    {{-- <button class="dialog__close-button close-button-message">Close</button> --}}
                    <div style="text-align: center">
                        <h2>{{ __('text.contact_us_thanks') }}</h2>
                        <br>
                        <p>{{ __('text.phone_request_mes_text') }}</p>
                    </div>
                </div>
            </div>
        </dialog>

        <div class="popup_white hide">
            <div class="popup_push">
                <div class="button_close">
                    <svg class="close_popup" width="15" height="15">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                    </svg>
                </div>
                <div class="popup_block">
                    <div class="popup_head">{{ __('text.common_push_head') }}</div>
                    <div class="popup_push_text">{{ __('text.common_push_text') }}</div>
                    <div class="push_buttons">
                        <div class="push_decline">{{ __('text.common_decline') }}</div>
                        <div class="push_allow">{{ __('text.common_allow') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="popup_gray" style="display: none">
            <div class="popup_call">
                <div class="button_close_message">
                    <svg class="close_popup" width="15" height="15">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                    </svg>
                </div>
                <div class="message_sended">
                    <h2>{{ __('text.contact_us_thanks') }}</h2>
                    <br>
                    <p>{{ __('text.phone_request_mes_text') }}</p>
                </div>
            </div>
        </div>

        <script>
            const routeRequestCall = "{{ route('home.request_call') }}";
            const routeRequestSubscribe = "{{ route('home.request_subscribe') }}";
            const routeRequestContactUs = "{{ route('home.request_contact_us') }}";
            const routeRequestAffiliate = "{{ route('home.request_affiliate') }}";

            const routeCartUp = "{{ route('cart.up') }}";
            const routeCartDown = "{{ route('cart.down') }}";
            const routeCartRemove = "{{ route('cart.remove') }}";
            const routeCartUpgrade = "{{ route('cart.upgrade') }}";
            const routeCartShipping = "{{ route('cart.shipping') }}";
            const routeCartBonus = "{{ route('cart.bonus') }}";

            const routeCheckCode = "{{ route('home.check_code') }}";
            const routeRequestLogin = "{{ route('home.request_login') }}";

            const routeSavePush = "{{ route('home.save_push_data') }}";
            const routeCart = "{{ route('cart.index') }}";
        </script>

        <script defer src="{{ asset("$design/js/app.js") }}"></script>
        <script defer src="{{ asset('js/all_js.js') }}"></script>

        @if ($web_statistic)
            <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
        @endif
    </body>
</html>