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
    <meta name="Description" content="@yield('description', 'Description')">
    <meta name="Keywords" content="@yield('keywords', 'Keywords')">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#161C8E" />
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

    @if (env('APP_PWA', 0))
        <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
        <script defer type="text/javascript" src="{{ asset_ver("js/sw-setup.js") }}"></script>
    @endif

    {{-- <script type="text/javascript" src="{{ asset("js/delete_cache.js") }}"></script> --}}

    {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

    <link href="{{ asset_ver($design . '/css/style.css') }}" rel="stylesheet">

    <script>
        const routeSearchAutocomplete = "{{ route('search.search_autocomplete') }}";
        const routeCartContent = "{{ route('cart.content') }}";
    </script>

    <script defer src="{{ asset('vendor/jquery/jquery-3.6.3.min.js') }}"></script>
    <script defer src="{{ asset_ver('vendor/jquery/autocomplete.js') }}"></script>
    <script defer src="{{ asset('vendor/jquery/init.js') }}"></script>
    <script defer type="text/javascript" src="{{ asset('js/jquery-migrate-1.2.1.min.js') }}"></script>
    {!! isset($pixel) ? $pixel : '' !!}
</head>

<body>
    <script>
        let flagc = false;
        let flagp = false;
        let flagm = false;
        const design = 7;
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
        <input type="hidden" class="design" value="{{ $design }}">
        <div class="popup_gray" style="display: none">
            <div class="popup_call">
                <div class="button_close">
                    <svg class="close_popup" width="15" height="15">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                    </svg>
                </div>
                <div class="popup_bottom">
                    <div class="popup_text">{{ __('text.common_callback') }}</div>
                    <div class="phone">
                        <div class="enter-info__country phone_code">
                            <select name="phone_code" class="form" data-scroll>
                                @foreach ($phone_codes as $item)
                                    <option id=""
                                        @if (empty(session('form'))) @selected($item['iso'] == session('location.country', ''))
                                    @else
                                        @selected($item['iso'] == session('form.phone_code', '')) @endif
                                        data-asset="{{ asset('style_checkout/images/countrys/sprite.svg#' . $item['nicename']) }}"
                                        value="+{{ $item['phonecode'] }}">
                                        +{{ $item['phonecode'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="enter-info__input enter-info__input--country">
                            <input required autocomplete="off" type="number" id="phone" name="phone"
                                placeholder="000 000 00 00" class="input" maxlength = "14"
                                oninput="maxLengthCheck(this)">
                        </div>
                    </div>
                    <div class="button_request_call">{{ __('text.common_callback') }}</div>
                </div>
                <div class="message_sended request hidden">
                    <h2>{{ __('text.contact_us_thanks') }}</h2>
                    <br>
                    <p>{{ __('text.phone_request_mes_text') }}</p>
                </div>
            </div>
        </div>
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
        <header class="header">

            {{-- <div class="christmas" style="display: none">
                <img loading="lazy" src="{{ asset("pub_images/pay_big.png") }}">
                <img loading="lazy" src="{{ asset("pub_images/christmas_big.png") }}">
                <img loading="lazy" src="{{ asset("pub_images/black_friday_big.png") }}">
            </div> --}}

            <div class="header__phones-top top-phones-header">
                <div class="header__container">
                    <div class="top-phones-header__items">
                        <div class="top-phones-header__item request" style="pointer-events: none; font-weight: 600"><a
                                class="request_call">{{ __('text.common_callback') }}</a>
                            <div class="request_text">{{ __('text.common_call_us_top') }}</div>
                        </div>
                        @foreach ($phone_arr as $id_phone => $phones)
                            <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_' . $id_phone)}}">{{__('text.phones_title_phone_' . $id_phone . '_code')}}{{__('text.phones_title_phone_' . $id_phone)}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="header__container header__container--second">
                <div class="header__body">
                    <div class="header__right">
                        <div class="header__top">
                            <div class="header__inner">
                                <div class="header__top-row">
                                    <a href="{{ route('home.index') }}" class="header__logo logo">
                                        <img loading="lazy" src="{{ asset("$design/images/logo.svg") }}" alt="">
                                    </a>
                                </div>
                                <div class="header__info-row">
                                    <div class="header__actions actions">
                                        @if (count($Language::GetAllLanuages()) > 1)
                                            <div class="actions__item">
                                                <div class="actions__icon">
                                                    <img loading="lazy" src="{{ asset("$design/images/icons/lang.svg") }}"
                                                        width="24" height="20" alt="">
                                                </div>
                                                <div class="actions__select">
                                                    <select name="form[]" class="form" id="lang_select"
                                                        onchange="location.href=this.options[this.selectedIndex].value"
                                                        data-scroll>
                                                        @foreach ($Language::GetAllLanuages() as $item)
                                                            <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}"
                                                                @if (App::currentLocale() == $item['code']) selected @endif>
                                                                {{ $item['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                        @if (count($Currency::GetAllCurrency()) > 1)
                                            <div class="actions__item">
                                                <div class="actions__icon">
                                                    <img loading="lazy" src="{{ asset("$design/images/icons/wallet.svg") }}"
                                                        width="24" height="20" alt="">
                                                </div>
                                                <div class="actions__select">
                                                    <select name="form[]" class="form" id="curr_select"
                                                        onchange="location.href=this.options[this.selectedIndex].value"
                                                        data-scroll>
                                                        @foreach ($Currency::GetAllCurrency() as $item)
                                                            <option value="{{ route('home.currency', $item['code']) }}"
                                                                @if (session('currency') == $item['code']) selected @endif>
                                                                {{ Str::upper($item['code']) }} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
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
                                    <button type="button" class="header__cart cart"
                                        @if ($cart_count != 0) onclick="location.href='{{ route('cart.index') }}'" @endif>
                                        <span class="cart__icon">
                                            <img loading="lazy" src="{{ asset("$design/images/icons/cart_blue.svg") }}"
                                                width="24" height="24">
                                            @if ($cart_count != 0)
                                                <span class="cart__quantity">{{ $cart_count }}</span>
                                            @endif
                                        </span>
                                        <span class="cart__total">{{ $Currency::convert($cart_total) }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @yield('content')

        <footer>
            <div class="header__phones-top top-phones-header footer">
                <div class="header__container">
                    <div class="top-phones-header__items">
                        <div class="top-phones-header__item request" style="pointer-events: none; font-weight: 600"><a
                                class="request_call">{{ __('text.common_callback') }}</a>
                            <div class="request_text">{{ __('text.common_call_us_top') }}</div>
                        </div>
                        @foreach ($phone_arr as $id_phone => $phones)
                            <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_' . $id_phone)}}">{{__('text.phones_title_phone_' . $id_phone . '_code')}}{{__('text.phones_title_phone_' . $id_phone)}}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            <p class="footer_copyright">
                {{ __('text.license_text_license1_1') }}
                {{ $domain }}
                {{ __('text.license_text_license1_2') }}
                {{ __('text.license_text_license2_d7') }}
            </p>

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
            </script>

            <script defer src="{{ asset_ver("$design/js/app.js") }}"></script>
            <script defer src="{{ asset_ver("$design/js/main.js") }}"></script>
            <script defer src="{{ asset_ver('js/all_js.js') }}"></script>

        </footer>

        @if ($web_statistic)
            <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
        @endif
</body>

</html>
