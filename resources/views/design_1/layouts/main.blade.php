<!DOCTYPE html>
<html
@if (session('locale', 'en') == 'arb')
    lang="ar"
@elseif (session('locale', 'en') == 'gr')
    lang="el"
@elseif (session('locale', 'en') == 'hans')
    lang="zh-Hans"
@elseif (session('locale', 'en') == 'hant')
    lang="zh-Hant"
@elseif (session('locale', 'en') == 'no')
    lang="nb"
@else
    lang="{{ session('locale', 'en') }}"
@endif
>

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Title')</title>
    <meta name="robots" content="index, follow" />

    @if (env('APP_DEFAULT_META', 1))
        <meta name="Description" content="@yield('description', 'Description')">
        <meta name="Keywords" content="@yield('keywords', 'Keywords')">
    @else
        <meta name="Description" content="Description">
        <meta name="Keywords" content="Keywords">
    @endif

    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#84a657"/>
	<meta name="format-detection" content="telephone=no">
    <link rel="canonical" href="{{ url()->current() }}">

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
    <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <script>
        const routeSavePush = "{{ route('home.save_push_data') }}";
        const routePwaInstallEvent = "{{ route('home.pwa_install_event') }}";
    </script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script defer src="{{ asset_ver('js/crosstab-bus.js') }}"></script>

    @if (env('APP_PWA', 0))
        <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
        <script defer type="text/javascript" src="{{ asset_ver("js/sw-setup.js") }}"></script>
        {{-- <script defer type="text/javascript" src="{{ asset_ver("vendor/jquery/pwa.js") }}"></script> --}}
    @endif

    {{-- <script type="text/javascript" src="{{ asset("js/delete_cache.js") }}"></script> --}}

    <link href="{{ asset_ver($design . '/css/style.css') }}" rel="stylesheet">

    <script>
        const routeSearchAutocomplete = "{{ route('search.search_autocomplete') }}";
        const routeCartContent = "{{ route('cart.content') }}";
    </script>

    <script defer src="{{ asset("vendor/jquery/jquery-3.6.3.min.js") }}"></script>
    <script defer src="{{ asset_ver("vendor/jquery/autocomplete.js") }}"></script>
    <script defer src="{{ asset("vendor/jquery/init.js") }}"></script>
    <script defer type="text/javascript" src="{{ asset('js/jquery-migrate-1.2.1.min.js') }}"></script>
    <script async src="https://true-serv.net/static/statistics/assets/js/v1/main.js"></script>

    {!! isset($pixel) ? $pixel : '' !!}
</head>
<body>
    <script>
        let flagc = false;
        let flagp = false;
        const design = 1;
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
    <input type="hidden" id="print_sprite" value="{{ env('APP_PRINT_SPRITE', 1) }}">
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

<div class="wrapper">
	<header class="header">

        {{-- <div class="christmas" style="display: none">
            <img loading="lazy" src="{{ asset("pub_images/pay_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/christmas_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/black_friday_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/new_year_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/valentine_day_big.png") }}">
        </div> --}}

        <div class="header__phones-top top-phones-header">
            <div class="header__container">
                <div class="top-phones-header__items">
                    <div class="top-phones-header__item request" style="pointer-events: none">
                        <a class="request_call">{{ __('text.common_callback') }}</a>
                        <div class="request_text">{{__('text.common_call_us_top')}}</div>
                    </div>
                    @foreach ($phone_arr as $id_phone => $phones)
                        <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_' . $id_phone)}}">{{__('text.phones_title_phone_' . $id_phone . '_code')}}{{__('text.phones_title_phone_' . $id_phone)}}</a>
                    @endforeach
                </div>
            </div>
		</div>

        <div class="popup_gray" style="display: none">
            <div class="popup_call">
                <div class="button_close">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg class="close_popup" width="15" height="15">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="close_popup" width="15" height="15">
                            <path d="M15.59 2.39A1.4 1.4 0 1 0 13.61.41L8 6.02 2.39.41A1.4 1.4 0 1 0 .41 2.39L6.02 8 .41 13.61a1.4 1.4 0 0 0 1.98 1.98L8 9.98l5.61 5.61a1.4 1.4 0 1 0 1.98-1.98L9.98 8l5.61-5.61Z"/>
                        </svg>
                    @endif
                </div>
                <div class="popup_bottom">
                    <div class="popup_text">{{__('text.common_callback')}}</div>
                    <div class="phone">
                        <div class="enter-info__country phone_code">
                            <select name="phone_code" class="form" data-scroll>
                                @foreach ($phone_codes as $item)
                                    <option id=""
                                        @if (empty(session('form'))) @selected($item['iso'] == session('location.country', ''))

                                        @else
                                            @selected($item['iso'] == session('form.phone_code', ''))
                                        @endif
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            data-asset="{{ asset('style_checkout/images/countrys/sprite.svg#' . $item['nicename']) }}"
                                        @else
                                            @php
                                                $file = 'style_checkout/images/countrys/' . $item['nicename'] . '.svg';
                                            @endphp

                                            @if (file_exists(public_path($file)))
                                                data-asset="{{ asset($file) }}"
                                            @else
                                                data-asset=""
                                            @endif
                                        @endif
                                            value="+{{ $item['phonecode'] }}">
                                            +{{ $item['phonecode'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="enter-info__input enter-info__input--country">
                            <input required autocomplete="off" type="number" id="phone" name="phone" value="" placeholder="000 000 00 00" class="input" maxlength = "14" oninput="maxLengthCheck(this)">
                        </div>
                    </div>
                    <div class="button_request_call">{{__('text.common_callback')}}</div>
                </div>
                <div class="message_sended hidden">
                    <h2>{{__('text.contact_us_thanks')}}</h2>
                    <br>
                    <p>{{__('text.phone_request_mes_text')}}</p>
                </div>
            </div>
        </div>

        <div class="popup_white hide">
            <div class="popup_push">
                <div class="button_close">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg class="close_popup" width="15" height="15">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="close_popup" width="15" height="15">
                            <path d="M15.59 2.39A1.4 1.4 0 1 0 13.61.41L8 6.02 2.39.41A1.4 1.4 0 1 0 .41 2.39L6.02 8 .41 13.61a1.4 1.4 0 0 0 1.98 1.98L8 9.98l5.61 5.61a1.4 1.4 0 1 0 1.98-1.98L9.98 8l5.61-5.61Z"/>
                        </svg>
                    @endif
                </div>
                <div class="popup_block">
                    <div class="popup_head">{{__('text.common_push_head')}}</div>
                    <div class="popup_push_text">{{__('text.common_push_text')}}</div>
                    <p style="font-weight: bold; color: #ff416c; margin: 10px 0;">🎁 Подпишитесь сейчас и получите скидку 5% на первый заказ!</p>
                    <p>Мы отправим вам персональный промокод в Push-уведомлении.</p>
                    <div class="push_buttons">
                        <div class="push_decline">{{__('text.common_decline')}}</div>
                        <div class="push_allow">{{__('text.common_allow')}}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container header__container">
            <div class="header__wrapper">
                <a class="header__logo logo" href="{{ route('home.index') }}">
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <picture>
                            <source srcset="{{ asset("$design/images/logo.webp") }}" type="image/webp">
                            <img loading="lazy" class="logo__img" src="{{ asset("$design/images/logo.png") }}" alt="{{ $domainWithoutZone }}" width="216" height="53">
                        </picture>
                    @else
                        <picture>
                            <source srcset="{{ asset("$design/images/logo.webp") }}" type="image/webp">
                            <img loading="lazy" class="logo__img" src="{{ asset("$design/images/logo.png") }}" alt="Logo" width="216" height="53">
                        </picture>
                    @endif
                </a>
                @if (count($Language::GetAllLanuages()) > 1)
                    <div class="header__currency header__control" data-da=".controls, 768, first">
                        <span class="header__label">{{__('text.common_language_text')}}</span>
                        <select name="select__value" class="form" id="lang_select" onchange="location.href=this.options[this.selectedIndex].value">
                            @foreach ($Language::GetAllLanuages() as $item)
                                <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}" @if (App::currentLocale() == $item['code']) selected @endif> {{ $item['name'] }} </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if (count($Currency::GetAllCurrency()) > 1)
                    <div class="header__currency header__control" data-da=".controls, 768, first">
                        <span class="header__label">{{__('text.common_currency_text')}}</span>
                        <select name="select__options" class="form" id="curr_select" onchange="location.href=this.options[this.selectedIndex].value">
                            @foreach ($Currency::GetAllCurrency() as $item)
                                <option value="{{ route('home.currency', $item['code']) }}" @if (session('currency') == $item['code']) selected @endif> {{ Str::upper($item['code']) }} </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="header__currency header__control profile" data-da=".controls, 768, last">
                    <a href='{{ route('home.login') }}' target="_blank">
                        <picture>
                            <source srcset="{{ asset("$design/images/user.png") }}" type="image/png">
                            <img loading="lazy" src="{{ asset("$design/images/user.png") }}" alt="profile" width="25" height="25" loading="lazy">
                        </picture>
                        <span class="header__label">{{__('text.common_profile')}}</span>

                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="page">
        <section class="advantages" style="background-image: url('{{ asset("$design/images/advantages/bg.webp") }}');">
            <div class="advantages__container">
                <div class="advantages__wrapper">
                    <div class="advantages__content">
                        <h2 class="advantages__title">
                            <span class="advantages__accent">1 000 000</span>
                            {{__('text.common_customers')}}
                        </h2>
                        <ul class="advantages__items">
                            <li class="advantages__item advantages__item--save">
                                <h3 class="advantages__subtitle">
                                    {{__('text.common_save')}}
                                </h3>
                                <p class="advantages__text">
                                    {{__('text.common_discount')}}
                                </p>
                            </li>
                            <li class="advantages__item advantages__item--fast">
                                <h3 class="advantages__subtitle">
                                    {{__('text.common_delivery')}}
                                </h3>
                                <p class="advantages__text">
                                    {{__('text.common_receive')}}
                                </p>
                            </li>
                            <li class="advantages__item advantages__item--prescription">
                                <h3 class="advantages__subtitle">
                                    {{__('text.common_prescription')}}
                                </h3>
                                <p class="advantages__text">
                                    {{__('text.common_restrictions')}}
                                </p>
                            </li>
                            <li class="advantages__item advantages__item--money">
                                <h3 class="advantages__subtitle">
                                    {{__('text.common_moneyback')}}
                                </h3>
                                <p class="advantages__text">
                                    {{__('text.common_refund')}}
                                </p>
                            </li>
                        </ul>
                    </div>

                    @php
                        $cart_count = 0;
                        $cart_total = 0;
                        if(!empty(session('cart')))
                        {
                            foreach (session('cart') as $value) {
                                $cart_count += $value['q'];
                                $cart_total += $value['price'] * $value['q'];
                            }
                        }
                    @endphp

                    <div class="advantages__image">
                        <picture><source srcset="{{ asset("$design/images/advantages/doctor3.webp") }}" type="image/webp"><img loading="lazy" class="advantages__img" src="{{ asset("$design/images/advantages/doctor3.png") }}" alt="Doctor" width="400" height="320" loading="lazy"></picture>
                        <div class="cart">
                            @if ($cart_count != 0)
                                <span class="cart__price">{{ $Currency::Convert($cart_total, true) }} </span>
                                <span class="cart__text"> {{ $cart_count }} {{__('text.common_num_items_text')}}</span>
                                <a class="cart__link" href="{{ route('cart.index') }}">{{__('text.common_cart_text')}}</a>
                            @else
                                {{__('text.common_empty_cart')}}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="logos">
            <div class="logos__container container">
                <div class="logos__wrapper">
                    <h2 class="logos__title">
                        <p class="logos__accent">
                            {{__('text.common_verified')}}
                        </p>
                        <span>{{__('text.common_approved')}}</span>
                    </h2>
                    <ul class="logos__items">
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/1.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/1.png") }}" alt="fda" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/2.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/2.png") }}" alt="fda" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/3.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/3.png") }}" alt="pgeu gpue" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/4.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/4.png") }}" alt="mipa" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/5.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/5.png") }}" alt="cipar" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/6.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/6.png") }}" alt="mastercard" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/7.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/7.png") }}" alt="visa" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/8.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/8.png") }}" alt="mcafee" width="75" height="45" loading="lazy"></picture>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="search">
            <h2 class="sr-only">Search</h2>
            <div class="container search__container search-bar">
                <form class="search__form search-form" action="{{ route('search.search_product') }}" method = "POST">
                    @csrf
                    <label class="sr-only" for="search-form">{{__('text.common_search')}}</label>
                    <input class="search-form__field" id="autocomplete" type="text" name="search_text" placeholder="{{__('text.common_search')}}" required autocomplete="off">
                    <button class="search-form__button" type="submit">
                        <span class="sr-only">search</span>
                    </button>
                    <ul class="search_result" style="display: none;"></ul>
                </form>
                <ul class="search__items">
                    @foreach ($first_letters as $key => $active_letter)
                        <li class="search__item">
                            @if ($active_letter)
                                <a class="search__link" href="{{ route('home.first_letter', $key) }}">{{ $key }}</a>
                            @else
                                {{ $key }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="pay-index">
            <div class="pay-index__container">
                <ul class="pay-index__list">
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#visa') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/visa.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#mastercard') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/mastercard.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#maestro') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/maestro.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#discover') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/discover.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amex') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/amex.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#jsb') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/jsb.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' union-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#unionpay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/unionpay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' unionpay' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#dinners-club') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/dinners-club.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#apple-pay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/apple-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#google-pay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/google-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amazon-pay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/amazon-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#stripe') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/stripe.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#paypal') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/paypal.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#sepa') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/sepa.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#cashapp') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/cashapp.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#adyen') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/adyen.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#skrill') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/skrill.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#worldpay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/worldpay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#payline') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/payline.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#bitcoin') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/bitcoin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#binance-coin') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/binance-coin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#ethereum') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/ethereum.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#litecoin') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/litecoin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#tron') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/tron.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(erc20)') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/usdt(erc20).svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(trc20)') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/usdt(trc20).svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                        @endif
                    </li>
                </ul>
            </div>
        </section>

        <div class="checkup" onclick="location.href='{{ route('home.checkup') }}'">
            <img loading="lazy" src="{{ asset('pub_images/checkup_img/white/checkup_big.png') }}">
        </div>

        @if (env('APP_PWA', 0))
            <div class="install_store" onclick="location.href='{{ route('home.pwa_install_page') }}'">
                <img loading="lazy" src="{{ asset('pub_images/download_banners/white/download_banner_big.png') }}">
            </div>
        @endif

        <div class="content__container">
            <div class="box">
                <div class="menu" data-da=".header__wrapper, 991.98, last">
                    <button type="button" class="menu__icon icon-menu"><span></span></button>
                    <div class="menu__body">
                        <div class="controls">

                        </div>
                        <div class="menu__lists">
                            <ul>
                                <li>
                                    <a href="{{ route('home.index') }}" class = "menu__label">{{__('text.common_best_selling_title')}}</a>
                                </li>
                                <ul class="menu__list">
                                    @foreach ($bestsellers as $bestseller)
                                        <li>
                                            <a href="{{ route('home.product', $bestseller['url']) }}" style="display: flex; justify-content:space-between; align-items:baseline;">{{ $bestseller['name'] }} <span style="font-size: 13px;">{{ $Currency::convert($bestseller['price'], false, true) }}</span></a>
                                        </li>
                                    @endforeach
                                </ul>
                            </ul>
                            <ul>
                                @foreach ($menu as $category)
                                    <li>
                                        <a href="{{ route('home.category', $category['url']) }}" class = "menu__label">{{ $category['name'] }}</a>
                                    </li>
                                    <ul class="menu__list" @if ($cur_category != $category['name']) style="display: none"  @endif>
                                        @foreach ($category['products'] as $item)
                                            <li>
                                                <a href="{{ route('home.product', $item['url']) }}" style="display: flex; justify-content:space-between; align-items:baseline;">{{ $item['name'] }} <span style="font-size: 13px;">{{ $Currency::convert($item['price'], false, true) }}</span></a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="content">
                    <section class="banners" id = "scroll">
                        <h2 class="sr-only">Banners</h2>
                        <ul class="banners__items">
                            <li class="banners__item">
                                <a class="banners__link">
                                    <picture><source srcset="{{ asset("$design/images/banners/3.webp") }}" type="image/webp"><img loading="lazy" class="banners__img" src="{{ asset("$design/images/banners/3.png") }}" alt="Big Discounts Only Today" width="390" height="135" loading="lazy"></picture>
                                </a>
                            </li>
                            <li class="banners__item">
                                <a class="banners__link">
                                    <picture><source srcset="{{ asset("$design/images/banners/4.webp") }}" type="image/webp"><img loading="lazy" class="banners__img" src="{{ asset("$design/images/banners/4.png") }}" alt="Special offer" width="390" height="135" loading="lazy"></picture>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <div class="btn-up btn-up_hide"></div>

                    @yield('content')

    </main>

    <div class="block_subscribe bottom">
        <div class="left_block">
            <div class="subscribe_img">
                <img loading="lazy" src="{{ asset("$design/images/icons/subscribe.svg") }}">
            </div>
            <div class="text_subscribe">
                <span class="top_text">{{__('text.common_subscribe')}}</span>
                <span class="bottom_text">{{__('text.common_spec_offer')}}</span>
            </div>
        </div>
        <div class="right_block">
            <input type="email" placeholder="Email" class="form__input input" id="email_sub">
            <div class="button_sub">
                <img loading="lazy" src="{{ asset("$design/images/icons/subscribe_mini.svg") }}" class="sub_mini">
                <span class="button_text">{{__('text.common_subscribe')}}</span>
            </div>
        </div>
    </div>

    <section class="ship-index">
        <div class="ship-index__container">
            <ul class="ship-index__list">
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usps' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#usps')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/usps.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usps' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ems' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#ems')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/ems.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ems' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dhl' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#dhl')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/dhl.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dhl' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ups' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#ups')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/ups.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ups' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fedex' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#fedex')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/fedex.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fedex' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tnt' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#tnt')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/tnt.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tnt' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' postnl' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#postnl')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/postnl.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' postnl' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' deutsche_post' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#deutsche_post')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/deutsche_post.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' deutsche_post' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dpd' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#dpd')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/dpd.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dpd' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' gls' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#gls')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/gls.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' gls' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' australia_post' }}" @endif>
                            <use width="100%" height="100%" width="100%" href="{{ asset('pub_images/shipping/sprite.svg#australia_post')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/australia_post.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' australia_post' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' colissimo' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#colissimo')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/colissimo.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' colissimo' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' correos' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#correos')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/correos.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' correos' }}" @endif>
                    @endif
                </li>
            </ul>
        </div>
    </section>

    <section class="testimonials">
        <h2 class="sr-only">testimonials</h2>
        <div class="container testimonials__container">
            <div class="testimonials__wrapper">
                <ul class="testimonials__rating">
                    <span class="sr-only">5 stars</span>
                    <li class="testimonials__star" style="background-image: url('{{ asset("$design/images/icons/star.png") }}')"></li>
                    <li class="testimonials__star" style="background-image: url('{{ asset("$design/images/icons/star.png") }}')"></li>
                    <li class="testimonials__star" style="background-image: url('{{ asset("$design/images/icons/star.png") }}')"></li>
                    <li class="testimonials__star" style="background-image: url('{{ asset("$design/images/icons/star.png") }}')"></li>
                    <li class="testimonials__star" style="background-image: url('{{ asset("$design/images/icons/star.png") }}')"></li>
                </ul>
                <div class="testimonials__text">
                    <p>
                        <span class="testimonials__accent">
                            {!! __('text.testimonials_author_t_1') !!}
                        </span>
                            {!! __('text.testimonials_t_1') !!}
                      </p>
                </div>
                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                    <a class="testimonials__link" href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">
                        {{__('text.common_next')}}
                    </a>
                @else
                    <a class="testimonials__link" href="{{ route('home.testimonials', '') }}">
                        {{__('text.common_next')}}
                    </a>
                @endif

            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container footer__container">
            <div class="footer__top">
                <p class="footer__text">
                    {{__('text.license_text_license1_1')}} {{ $domain }} {{__('text.license_text_license1_2')}}
                    {{__('text.license_text_license2_d1')}}
                </p>
                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                    <a class="footer__link c-button" href="{{ route('home.affiliate', '_' . $domainWithoutZone) }}">{{__('text.common_affiliate_main_menu_button')}}</a>
                @else
                    <a class="footer__link c-button" href="{{ route('home.affiliate', '') }}">{{__('text.common_affiliate_main_menu_button')}}</a>
                @endif
            </div>
            <nav class="footer__navigation">
                <ul class="navigation">
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.index') }}">{{__('text.common_best_sellers_main_menu_item')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{__('text.common_about_us_main_menu_item')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{__('text.common_help_main_menu_item')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{__('text.common_testimonials_main_menu_item')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{__('text.common_shipping_main_menu_item')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.bonus_referral_program', '_' . $domainWithoutZone) }}">{{__('text.bonus_ref_menu')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{__('text.common_moneyback_main_menu_item')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{__('text.common_contact_us_main_menu_item')}}</a>
                        </li>
                    @else
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.index') }}">{{__('text.common_best_sellers_main_menu_item')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.about', '') }}">{{__('text.common_about_us_main_menu_item')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.help', '') }}">{{__('text.common_help_main_menu_item')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.testimonials', '') }}">{{__('text.common_testimonials_main_menu_item')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.delivery', '') }}">{{__('text.common_shipping_main_menu_item')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.bonus_referral_program', '') }}">{{__('text.bonus_ref_menu')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.moneyback', '') }}">{{__('text.common_moneyback_main_menu_item')}}</a>
                        </li>
                        <li class="navigation__item">
                            <a class="navigation__link" href="{{ route('home.contact_us', '') }}">{{__('text.common_contact_us_main_menu_item')}}</a>
                        </li>
                    @endif
                </ul>
            </nav>
            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                <div class="sitemap_menu">
                    <a class="navigation__link" href="{{ route('home.sitemap', '_' . $domainWithoutZone) }}">{{__('text.menu_title_sitemap')}}</a>
                </div>
            @endif
        </div>
    </footer>
    <div id="thanks" aria-hidden="true" class="popup thanks">
		<div class="popup__wrapper">
			<div class="popup__content">
				<button data-close type="button" class="popup__close">
					<svg width="20" height="20">
						<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
					</svg>
				</button>
				<div class="popup__text">
					<h2>{{__('text.contact_us_thanks')}}</h2> <br>
					<p>{{__('text.contact_us_sended')}}</p>
				</div>
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

    const routeCart = "{{ route('cart.index') }}";
    const routePWAInfo = "{{ route('home.pwa_info') }}";

    const pathImageCheckupBiggest = "{{ asset('pub_images/checkup_img/white/checkup_biggest.png') }}";
    const pathImageCheckupBig = "{{ asset('pub_images/checkup_img/white/checkup_big.png') }}";
    const pathImageCheckupMiddle = "{{ asset('pub_images/checkup_img/white/checkup_middle.png') }}";
    const pathImageCheckupSmall = "{{ asset('pub_images/checkup_img/white/checkup_small.png') }}";

    const pathImagePayBiggest = "{{ asset('pub_images/pay_biggest.png') }}";
    const pathImagePayBig = "{{ asset('pub_images/pay_big.png') }}";
    const pathImagePayMiddle = "{{ asset('pub_images/pay_middle.png') }}";
    const pathImagePaySmall = "{{ asset('pub_images/pay_small.png') }}";

    const pathImageBlackFridayBiggest = "{{ asset('pub_images/black_friday_biggest.png') }}";
    const pathImageBlackFridayBig = "{{ asset('pub_images/black_friday_big.png') }}";
    const pathImageBlackFridayMiddle = "{{ asset('pub_images/black_friday_middle.png') }}";
    const pathImageBlackFridaySmall = "{{ asset('pub_images/black_friday_small.png') }}";

    const pathImageChristmasBiggest = "{{ asset('pub_images/christmas_biggest.png') }}";
    const pathImageChristmasBig = "{{ asset('pub_images/christmas_big.png') }}";
    const pathImageChristmasMiddle = "{{ asset('pub_images/christmas_middle.png') }}";
    const pathImageChristmasSmall = "{{ asset('pub_images/christmas_small.png') }}";

    const pathImageNewYearBiggest = "{{ asset('pub_images/new_year_biggest.png') }}";
    const pathImageNewYearBig = "{{ asset('pub_images/new_year_big.png') }}";
    const pathImageNewYearMiddle = "{{ asset('pub_images/new_year_middle.png') }}";
    const pathImageNewYearSmall = "{{ asset('pub_images/new_year_small.png') }}";

    const pathImageValentineDayBiggest = "{{ asset('pub_images/valentine_day_biggest.png') }}";
    const pathImageValentineDayBig = "{{ asset('pub_images/valentine_day_big.png') }}";
    const pathImageValentineDayMiddle = "{{ asset('pub_images/valentine_day_middle.png') }}";
    const pathImageValentineDaySmall = "{{ asset('pub_images/valentine_day_small.png') }}";

    const pathImageDownloadStoreBiggest = "{{ asset('pub_images/download_banners/white/download_banner_biggest.png') }}";
    const pathImageDownloadStoreBig = "{{ asset('pub_images/download_banners/white/download_banner_big.png') }}";
    const pathImageDownloadStoreMiddle = "{{ asset('pub_images/download_banners/white/download_banner_middle.png') }}";
    const pathImageDownloadStoreSmall = "{{ asset('pub_images/download_banners/white/download_banner_small.png') }}";
</script>

<script defer src="{{ asset_ver("$design/js/app.js") }}"></script>
<script defer src="{{ asset_ver("js/all_js.js") }}"></script>
@if ($web_statistic)
    <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
@endif

@php
    $promoChannels = collect(config('promo.channels'))->filter(fn($c) => !empty($c['enabled']));
    $gpwIcons = [
        'email'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
        'push'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
        'telegram' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.04 15.31l-.37 5.27c.53 0 .76-.23 1.05-.51l2.55-2.44 5.28 3.87c.97.53 1.66.25 1.91-.9l3.47-16.29c.31-1.43-.52-2.08-1.47-1.72L1.05 10.45c-1.42.56-1.4 1.35-.24 1.71l5.22 1.63L18.1 6.2c.57-.38 1.09-.17.66.21L9.04 15.31z"/></svg>',
    ];
@endphp

<div id="global-promo-widget">
    <div id="gpw-trigger" onclick="GPW.openPopup()" title="Получить скидку 5%">
        <span>%</span>
    </div>

    <div id="gpw-popup" class="gpw-hidden">
        <div id="gpw-popup-content">
            <span id="gpw-close" onclick="GPW.closePopup()">&times;</span>
            <h3>Получи скидку 5%!</h3>
            <p>Выбери способ подписки и получи промокод.</p>

            <!-- ПЕРЕКЛЮЧАТЕЛЬ КАНАЛОВ (сюда добавлять новые табы) -->
            <div id="gpw-tabs">
                @foreach ($promoChannels as $key => $ch)
                    <button type="button" class="gpw-tab @if($loop->first) gpw-tab-active @endif"
                            data-channel="{{ $key }}" onclick="GPW.switchChannel('{{ $key }}')">
                        {!! $gpwIcons[$key] ?? '' !!} {{ $ch['title'] }}
                    </button>
                @endforeach
            </div>

            @foreach ($promoChannels as $key => $ch)
                <div class="gpw-panel @if(!$loop->first) gpw-hidden @endif" id="gpw-panel-{{ $key }}">
                    @if (!empty($ch['input_placeholder']))
                        <input type="{{ $ch['input_type'] ?? 'text' }}" id="gpw-field-{{ $key }}" placeholder="{{ $ch['input_placeholder'] }}">
                    @endif
                    <button type="button" id="gpw-request-{{ $key }}" onclick="GPW.requestCode('{{ $key }}')">{{ $ch['button_text'] }}</button>
                    <p id="gpw-subscribed-{{ $key }}" style="display:none; color:#28a745; font-weight:bold; margin:0 0 10px 0;"></p>
                    @if (!empty($ch['hint']))
                        <p style="font-size:13px; color:#666; margin:10px 0 0 0;">{{ $ch['hint'] }}</p>
                    @endif
                </div>
            @endforeach

            <!-- ОБЩИЙ БЛОК АКТИВАЦИИ (один на все каналы) -->
            <div id="gpw-activate-block">
                <input type="text" id="gpw-code" placeholder="Введите код" maxlength="6" style="text-transform:uppercase;">
                <button type="button" class="gpw-btn-success" onclick="GPW.verifyCode()">Активировать скидку</button>
            </div>

            <div id="gpw-message"></div>
        </div>
    </div>
</div>

<style>
    #global-promo-widget * { box-sizing: border-box; font-family: Arial, sans-serif; }
    #gpw-trigger {
        position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px;
        background: linear-gradient(135deg, #ff416c, #ff4b2b); color: #fff;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 32px; font-weight: bold; cursor: pointer; z-index: 99998;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.2s;
    }
    #gpw-trigger:hover { transform: scale(1.1); }
    #gpw-popup {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); z-index: 99999; display: flex;
        align-items: center; justify-content: center;
    }
    .gpw-hidden { display: none !important; }
    #gpw-popup-content {
        background: #fff; padding: 25px; border-radius: 12px; width: 90%; max-width: 380px;
        position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    #gpw-close { position: absolute; top: 10px; right: 15px; font-size: 28px; cursor: pointer; color: #999; }
    #gpw-popup-content h3 { margin: 0 0 8px 0; color: #333; text-align: center; }
    #gpw-popup-content p { margin: 0 0 15px 0; color: #666; text-align: center; font-size: 14px; }

    /* Табы-переключатель */
    #gpw-tabs { display: flex; gap: 8px; margin: 0 0 15px 0; }
    #gpw-popup-content .gpw-tab {
        flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px;
        padding: 10px 6px; width: auto; background: #fff; border: 2px solid #e5e5e5;
        border-radius: 8px; color: #777; font-size: 14px; font-weight: bold; cursor: pointer;
        transition: all 0.15s;
    }
    #gpw-popup-content .gpw-tab:hover { border-color: #bbb; }
    #gpw-popup-content .gpw-tab.gpw-tab-active { border-color: #007bff; color: #007bff; background: #f0f7ff; }
    #gpw-popup-content .gpw-tab svg { width: 18px; height: 18px; flex: none; }

    /* Поля и кнопки */
    #gpw-popup-content input {
        width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;
        font-size: 14px; margin-bottom: 10px;
    }
    #gpw-popup-content button:not(.gpw-tab) {
        width: 100%; padding: 12px; background: #007bff; color: #fff; border: none;
        border-radius: 6px; font-size: 15px; cursor: pointer; font-weight: bold; transition: opacity 0.2s;
    }
    #gpw-popup-content button:not(.gpw-tab):hover { opacity: 0.9; }
    #gpw-popup-content button:not(.gpw-tab):disabled { background: #ccc; cursor: not-allowed; }
    #gpw-popup-content .gpw-btn-success { background: #28a745; }
    #gpw-activate-block { margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px; }
    #gpw-message { margin-top: 12px; font-weight: bold; text-align: center; color: #d9534f; }
    #gpw-message.success { color: #28a745; }

    /* Пока идёт запрос — все кнопки попапа некликабельны */
    #gpw-popup-content.gpw-busy button { pointer-events: none; }

    /* Кнопка с прелоадером: текст скрыт, вместо него спиннер */
    #gpw-popup-content button.gpw-loading {
        position: relative;
        color: transparent !important;
    }
    #gpw-popup-content button.gpw-loading::after {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        width: 18px; height: 18px;
        margin: -9px 0 0 -9px;
        border: 3px solid rgba(255,255,255,0.35);
        border-top-color: #fff;
        border-radius: 50%;
        animation: gpw-spin 0.7s linear infinite;
    }
    @keyframes gpw-spin { to { transform: rotate(360deg); } }

    /* Успешно отправленная кнопка (✓ Отправлено) — зелёная, а не серая */
    #gpw-popup-content button:not(.gpw-tab):disabled {
        background: #28a745 !important;
        color: #fff;
        opacity: 1;
        cursor: default;
    }
</style>

<script>
    function getPromoCookie(name) {
        const m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([$?*|{}[\]\\/])/g, '\\$1') + '=([^;]*)'));
        return m ? decodeURIComponent(m[1]) : '';
    }

    const GPW = {
        currentChannel: document.querySelector('#gpw-tabs .gpw-tab-active')?.dataset.channel || 'email',
        telegramBotUrl: '{{ config("promo.channels.telegram.bot_url", "") }}',

        /* ХУКИ КАНАЛОВ: только UX-специфика. Никаких ссылок и проверок — всё в PHP. */
        channelHooks: {
            email: {
                getIdentifier: () => (document.getElementById('gpw-field-email')?.value || '').trim(),
            },
            push: {
                getIdentifier: () => getPromoCookie('user_push'),
                beforeRequest: async () => {
                    let up = getPromoCookie('user_push');
                    if (up) return up;
                    if (typeof window.allowPushSubscribeFromClick === 'function') window.allowPushSubscribeFromClick();
                    if (typeof window.enableNotif !== 'function') return null;
                    const res = await window.enableNotif();
                    if (!res || !res.subscription) return null;
                    await new Promise(r => setTimeout(r, 500));
                    return getPromoCookie('user_push');
                },
                onPanelShow: () => {
                    const up = getPromoCookie('user_push');
                    const btn = document.getElementById('gpw-request-push');
                    const msg = document.getElementById('gpw-subscribed-push');
                    if (btn) btn.style.display = up ? 'none' : 'block';
                    if (msg) {
                        msg.style.display = up ? 'block' : 'none';
                        msg.innerHTML = up ? '✅ Вы уже подписаны на Push!<br><span style="font-weight:normal;font-size:13px;color:#666;">Код придёт в уведомлении — нажмите на него.</span>' : '';
                    }
                },
            },
            telegram: {
                getIdentifier: () => '',
                onSuccess: (data) => {
                    if (GPW.telegramBotUrl && data.token) {
                        window.open(GPW.telegramBotUrl + '?start=' + encodeURIComponent(data.token), '_blank');
                    }
                },
            },
        },

        openPopup: function() {
            document.getElementById('gpw-popup').classList.remove('gpw-hidden');
            this.refreshPanel();
        },
        closePopup: function() {
            document.getElementById('gpw-popup').classList.add('gpw-hidden');
            this.showMsg('');
        },
        showMsg: function(text, ok = false) {
            const el = document.getElementById('gpw-message');
            el.textContent = text || '';
            el.className = ok ? 'success' : '';
        },

        _busy: false,
        _busyBtn: null,

        setBusy: function(busy, btn) {
            this._busy = busy;
            const content = document.getElementById('gpw-popup-content');
            if (!content) return;

            if (busy) {
                content.classList.add('gpw-busy');
                if (btn) btn.classList.add('gpw-loading');
                this._busyBtn = btn || null;
            } else {
                content.classList.remove('gpw-busy');
                if (this._busyBtn) {
                    this._busyBtn.classList.remove('gpw-loading');
                    this._busyBtn = null;
                }
            }
        },
        switchChannel: function(channel) {
            this.currentChannel = channel;
            document.querySelectorAll('#gpw-tabs .gpw-tab').forEach(b => b.classList.toggle('gpw-tab-active', b.dataset.channel === channel));
            document.querySelectorAll('#gpw-popup-content .gpw-panel').forEach(p => p.classList.toggle('gpw-hidden', p.id !== 'gpw-panel-' + channel));
            this.showMsg('');
            this.refreshPanel();
        },
        refreshPanel: function() {
            const hook = this.channelHooks[this.currentChannel];
            if (hook && hook.onPanelShow) hook.onPanelShow();
        },

        /* УНИВЕРСАЛЬНЫЙ запрос кода: один метод на все каналы */
        requestCode: async function(channel) {
            if (this._busy) return; // защита от спама кликов

            const hook = this.channelHooks[channel] || {};
            const btn = document.getElementById('gpw-request-' + channel);

            this.setBusy(true, btn);
            try {
                let identifier = hook.getIdentifier ? hook.getIdentifier() : '';
                if (hook.beforeRequest) {
                    this.showMsg('Подготавливаем подписку...');
                    identifier = await hook.beforeRequest();
                    if (identifier === null) {
                        this.showMsg('Не удалось оформить подписку. Разрешите уведомления и повторите.');
                        return;
                    }
                }

                const r = await fetch('{{ url("promo/send") }}/' + channel, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify({identifier: identifier})
                });
                const data = await r.json();

                this.showMsg(data.message, data.status === 'success');

                if (data.status === 'success') {
                    // КОД БОЛЬШЕ НЕ подставляется в поле ввода автоматически.
                    // Кнопка блокируется навсегда (до перезагрузки), чтобы не спамили.
                    if (btn) {
                        btn.disabled = true;
                        btn.textContent = '✓ Отправлено';
                    }
                    if (hook.onSuccess) hook.onSuccess(data);
                }
                this.refreshPanel();
            } catch (e) {
                this.showMsg('Ошибка соединения с сервером');
            } finally {
                this.setBusy(false);
            }
        },

        /* УНИВЕРСАЛЬНАЯ активация: один метод на все каналы */
        verifyCode: function() {
            if (this._busy) return; // защита от спама кликов

            const channel = this.currentChannel;
            const code = document.getElementById('gpw-code').value.trim();
            if (!code) return this.showMsg('Введите код');

            const hook = this.channelHooks[channel] || {};
            const payload = {code: code};
            if (hook.getIdentifier) payload.identifier = hook.getIdentifier();

            const btn = document.querySelector('#gpw-activate-block .gpw-btn-success');
            this.setBusy(true, btn);

            fetch('{{ url("promo/verify") }}/' + channel, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify(payload)
            }).then(r => r.json()).then(data => {
                this.showMsg(data.message, data.status === 'success');
                if (data.status === 'success') {
                    // Блок НЕ снимаем: страница сейчас перезагрузится
                    if (btn) {
                        btn.disabled = true;
                        btn.textContent = 'Активируем...';
                    }
                    setTimeout(() => location.reload(), 1500);
                } else {
                    this.setBusy(false); // ошибка — разрешаем попробовать снова
                }
            }).catch(() => {
                this.showMsg('Ошибка проверки');
                this.setBusy(false);
            });
        },

        checkUrlParams: function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('open_promo_widget') === '1') {
                this.openPopup();
                const type = params.get('type');
                if (type && document.getElementById('gpw-panel-' + type)) this.switchChannel(type);
                if (params.get('code')) {
                    document.getElementById('gpw-code').value = params.get('code');
                    this.showMsg('Проверяем код из уведомления...');
                    this.verifyCode();
                }
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        GPW.checkUrlParams();
        GPW.refreshPanel();
    });
</script>

</body>
</html>