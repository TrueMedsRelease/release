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
    <meta name="theme-color" content="#4494DE" />
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
    <script async src="https://true-serv.net/static/statistics/assets/js/v1/main.js"></script>
</head>

<body>
    <script>
        let flagc = false;
        let flagp = false;
        const design = 9;
    </script>

    @if (session('locale'))
        <input type="hidden" id="lang_session" value="{{ session('locale') }}">
    @endif
    @if (session('order'))
        <input type="hidden" id="order_info_session" value="{{ json_encode(session('order')) }}">
    @endif

    @php
        $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
    @endphp

    <input type="hidden" id="is_pwa_here" value="{{ env('APP_PWA', 0) }}">
    <input type="hidden" id="vapid_pub" value="{{ base64_encode(env('VAPID_PUBLIC_KEY', '')) }}">
    <input type="hidden" id="subsc_popup" value="{{ env('SUBSCRIBE_POPUP_STATUS', 1) }}">
    <input type="hidden" id="print_sprite" value="{{ env('APP_PRINT_SPRITE', 1) }}">
    <input type="hidden" id="country_iso" value="{{ $codes }}">
    <input type="hidden" id="initial_country" value="{{ strtolower(session('location.country')) }}">

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

        {{-- <div class="christmas" style="display: none">
            <img loading="lazy" src="{{ asset("pub_images/pay_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/christmas_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/black_friday_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/new_year_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/valentine_day_big.png") }}">
        </div> --}}

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
                    <div class="popup_text">{{ __('text.common_callback') }}</div>
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
                            <input required autocomplete="off" type="number" id="phone" name="phone"
                                placeholder="000 000 00 00" class="input" maxlength = "14"
                                oninput="maxLengthCheck(this)">
                        </div>
                    </div>
                    <div class="button_request_call">{{ __('text.common_callback') }}</div>
                </div>
                <div class="message_sended hidden">
                    <h2>{{ __('text.contact_us_thanks') }}</h2>
                    <br>
                    <p>{{ __('text.phone_request_mes_text') }}</p>
                </div>
            </div>
        </div>
        <header class="header">
            <div class="header__phones-top top-phones-header">
                <div class="header__container">
                    <div class="top-phones-header__items">
                        <div class="top-phones-header__item request" style="pointer-events: none">
                            <a class="request_call">{{ __('text.common_callback') }}</a>
                            <div class="request_text">{{ __('text.common_call_us_top') }}</div>
                        </div>
                        @foreach ($phone_arr as $id_phone => $phones)
                            <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_' . $id_phone)}}">{{__('text.phones_title_phone_' . $id_phone . '_code')}}{{__('text.phones_title_phone_' . $id_phone)}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="container header__container">
                <div class="header__top">
                    <div class="header__inner">
                        <a href="{{ route('home.index') }}" class="header__logo logo">
                            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                <img class="logo__icon" src="{{ asset("$design/images/logo.svg") }}" alt="{{ $domainWithoutZone }}">
                            @else
                                <img class="logo__icon" src="{{ asset("$design/images/logo.svg") }}" alt="Logo">
                            @endif
                        </a>
                        <form class="header__search" data-da=".header__top, 1024, last"
                            action="{{ route('search.search_product') }}" method = "POST" data-dev>
                            @csrf
                            <div class="search search-bar">
                                <div class="search__input">
                                    <input id="autocomplete" autocomplete="off" type="text" name="search_text"
                                        placeholder="{{ __('text.common_search') }}">
                                </div>
                                <button class="search__icon" type="submit">
                                    <span class="visually-hidden">search</span>
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="20" height="20">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-search") }}">
                                            </use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="20" height="20">
                                            <path d="M8.25 15C11.9779 15 15 11.9779 15 8.25C15 4.52208 11.9779 1.5 8.25 1.5C4.52208 1.5 1.5 4.52208 1.5 8.25C1.5 11.9779 4.52208 15 8.25 15Z"
                                                    stroke="#4494DE"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"/>
                                            <path d="M16.5 16.5L13.5 13.5"
                                                    stroke="#4494DE"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"/>
                                        </svg>
                                    @endif
                                </button>
                            </div>
                        </form>
                        <div class="header__actions actions">
                            @if (count($Language::GetAllLanuages()) > 1)
                                <div class="actions__item">
                                    <div class="actions__icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="20" height="20">
                                                <use
                                                    xlink:href="{{ asset("$design/images/icons/icons.svg#svg-global") }}">
                                                </use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21" fill="currentColor" width="20" height="20">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.45513 1.35549C5.71534 1.86156 4.16862 2.88057 3.01701 4.27943C1.86541 5.67828 1.16246 7.39193 1 9.19654H5.11401C5.35251 6.4505 6.14868 3.78199 7.45385 1.35421L7.45513 1.35549ZM5.11401 10.8036H1C1.16212 12.6082 1.86477 14.3221 3.01615 15.7211C4.16753 17.1202 5.7141 18.1395 7.45385 18.6459C6.14868 16.2181 5.35251 13.5496 5.11401 10.8036ZM9.516 18.9892C7.95627 16.5175 7.00053 13.7133 6.72618 10.8036H13.1993C12.925 13.7133 11.9692 16.5175 10.4095 18.9892C10.1118 19.0036 9.81366 19.0036 9.516 18.9892ZM12.4729 18.6446C14.2125 18.1383 15.7589 17.1192 16.9103 15.7204C18.0617 14.3215 18.7644 12.608 18.9268 10.8036H14.8128C14.5743 13.5496 13.7781 16.2181 12.4729 18.6459V18.6446ZM14.8128 9.19654H18.9268C18.7647 7.39187 18.062 5.67805 16.9106 4.27896C15.7593 2.87988 14.2127 1.86057 12.4729 1.35421C13.7781 3.78198 14.5743 6.4505 14.8128 9.19654ZM9.516 1.01095C9.81408 0.996351 10.1127 0.996351 10.4108 1.01095C11.9701 3.48271 12.9254 6.28692 13.1993 9.19654H6.72747C7.00645 6.26916 7.96424 3.46264 9.516 1.01095Z"/>
                                            </svg>
                                        @endif
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
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="20" height="20">
                                                <use
                                                    xlink:href="{{ asset("$design/images/icons/icons.svg#svg-wallet") }}">
                                                </use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 19" fill="currentColor" width="20" height="20">
                                                <path d="M15.8286 3.86902C15.3175 3.70793 14.7433 3.62972 14.0798 3.58878C13.3215 3.54199 12.3999 3.54199 11.2692 3.54199H8.71712C7.38096 3.54199 6.33349 3.54198 5.49667 3.62155C4.64371 3.70265 3.94529 3.8708 3.32961 4.24809C2.68486 4.64319 2.14278 5.18527 1.74768 5.83002C1.73368 5.85286 1.71997 5.87582 1.70655 5.89889C1.77096 4.69627 1.9392 3.89976 2.37862 3.26922C2.60858 2.93924 2.88693 2.64906 3.20344 2.40932C4.18347 1.66699 5.58147 1.66699 8.37748 1.66699H12.8514C14.2574 1.66699 14.9604 1.66699 15.3972 2.12237C15.7327 2.47211 15.8105 2.98556 15.8286 3.86902Z"/>
                                                <path d="M2.2805 6.15658C1.6665 7.15854 1.6665 8.52247 1.6665 11.2503C1.6665 13.9782 1.6665 15.3421 2.2805 16.3441C2.62407 16.9047 3.09545 17.3761 3.65609 17.7197C4.65805 18.3337 6.02198 18.3337 8.74984 18.3337H11.2498C13.9777 18.3337 15.3416 18.3337 16.3436 17.7197C16.9042 17.3761 17.3756 16.9047 17.7192 16.3441C18.1224 15.686 18.2608 14.8719 18.3083 13.6178H13.0302C11.4298 13.6178 10.1325 12.3204 10.1325 10.72C10.1325 9.11967 11.4298 7.82231 13.0302 7.82231H18.2312C18.1477 7.1326 17.9957 6.60787 17.7192 6.15658C17.3756 5.59593 16.9042 5.12456 16.3436 4.78099C16.184 4.68319 16.0152 4.60096 15.8332 4.53183C14.8725 4.16699 13.5432 4.16699 11.2498 4.16699H8.74984C6.02198 4.16699 4.65805 4.16699 3.65609 4.78099C3.09545 5.12456 2.62407 5.59593 2.2805 6.15658Z"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M18.3147 9.07231H13.0302C12.1202 9.07231 11.3825 9.81002 11.3825 10.72C11.3825 11.6301 12.1202 12.3678 13.0302 12.3678H18.3312C18.3332 12.0226 18.3332 11.6512 18.3332 11.2503C18.3332 10.4026 18.3332 9.68665 18.3147 9.07231ZM13.0302 10.095C12.685 10.095 12.4052 10.3749 12.4052 10.72C12.4052 11.0652 12.685 11.345 13.0302 11.345H15.3029C15.6481 11.345 15.9279 11.0652 15.9279 10.72C15.9279 10.3749 15.6481 10.095 15.3029 10.095H13.0302Z"/>
                                            </svg>
                                        @endif
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
                            <div class="actions__item actions__item--order" data-da=".fixed-bar, 600, last">
                                <a href='{{ route('home.login') }}' target="_blank">
                                    <div class="actions__icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="20" height="20">
                                                <use
                                                    xlink:href="{{ asset("$design/images/icons/icons.svg#svg-profile") }}">
                                                </use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 21" fill="currentColor" width="20" height="20">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.0415 10.0003C1.0415 5.05277 5.05229 1.04199 9.99984 1.04199C14.9474 1.04199 18.9582 5.05277 18.9582 10.0003C18.9582 12.5407 17.8999 14.835 16.2019 16.4645C14.5929 18.0087 12.4067 18.9587 9.99984 18.9587C7.59302 18.9587 5.40678 18.0087 3.79776 16.4645C2.09975 14.835 1.0415 12.5407 1.0415 10.0003ZM15.0691 15.8074C14.7099 14.733 13.6947 13.9587 12.4998 13.9587H7.49984C6.30495 13.9587 5.28976 14.733 4.93056 15.8074C6.28611 16.9919 8.05869 17.7087 9.99984 17.7087C11.941 17.7087 13.7136 16.9919 15.0691 15.8074ZM9.99984 3.54199C7.81371 3.54199 6.0415 5.3142 6.0415 7.50033C6.0415 9.68645 7.81371 11.4587 9.99984 11.4587C12.186 11.4587 13.9582 9.68645 13.9582 7.50033C13.9582 5.3142 12.186 3.54199 9.99984 3.54199Z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="actions__label">{{ __('text.common_profile') }}</div>
                                </a>
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
                        <a href="{{ route('cart.index') }}" type="button" class="header__cart cart"
                            data-da=".fixed-bar, 600, 2">
                            @if ($cart_count != 0)
                                <span class="cart__icon">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="24" height="24">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}">
                                            </use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M13.4697 0.46967C13.7626 0.176777 14.2375 0.176777 14.5304 0.46967L18.3107 4.25001H21C21.4142 4.25001 21.75 4.5858 21.75 5.00001C21.75 5.41422 21.4142 5.75001 21 5.75001H20.4557C20.2293 6.60606 19.9365 7.59301 19.5959 8.74137L18.5708 12.1969C18.1636 13.5702 17.914 14.412 17.4765 15.0966C16.7233 16.2751 15.5663 17.1386 14.2223 17.5256C13.4416 17.7503 12.5635 17.7502 11.1312 17.75H10.8688C9.43649 17.7502 8.55843 17.7503 7.77772 17.5256C6.43365 17.1386 5.2767 16.2751 4.52349 15.0966C4.08598 14.412 3.83637 13.5702 3.42921 12.1969L2.40431 8.74199C2.06357 7.59338 1.77073 6.60621 1.54431 5.75001H1C0.585786 5.75001 0.25 5.41422 0.25 5.00001C0.25 4.5858 0.585786 4.25001 1 4.25001H3.93198L7.71231 0.469685C8.0052 0.176792 8.48008 0.176792 8.77297 0.469685C9.06586 0.762578 9.06586 1.23745 8.77297 1.53035L6.05331 4.25001H16.1894L13.4697 1.53033C13.1768 1.23744 13.1768 0.762563 13.4697 0.46967ZM9.75 9.00002C9.75 8.5858 9.41421 8.25002 9 8.25002C8.58579 8.25002 8.25 8.5858 8.25 9.00002V13C8.25 13.4142 8.58579 13.75 9 13.75C9.41421 13.75 9.75 13.4142 9.75 13V9.00002ZM13.75 9.00002C13.75 8.5858 13.4142 8.25002 13 8.25002C12.5858 8.25002 12.25 8.5858 12.25 9.00002V13C12.25 13.4142 12.5858 13.75 13 13.75C13.4142 13.75 13.75 13.4142 13.75 13V9.00002Z"/>
                                        </svg>
                                    @endif
                                    <span class="cart__quantity">{{ $cart_count }}</span>
                                </span>
                                <span class="cart__text">{{ __('text.common_cart_text_d2') }}</span>
                                <span class="cart__total">{{ $Currency::convert($cart_total) }}</span>
                            @else
                                <span class="cart__icon">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="20" height="20">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}">
                                            </use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M13.4697 0.46967C13.7626 0.176777 14.2375 0.176777 14.5304 0.46967L18.3107 4.25001H21C21.4142 4.25001 21.75 4.5858 21.75 5.00001C21.75 5.41422 21.4142 5.75001 21 5.75001H20.4557C20.2293 6.60606 19.9365 7.59301 19.5959 8.74137L18.5708 12.1969C18.1636 13.5702 17.914 14.412 17.4765 15.0966C16.7233 16.2751 15.5663 17.1386 14.2223 17.5256C13.4416 17.7503 12.5635 17.7502 11.1312 17.75H10.8688C9.43649 17.7502 8.55843 17.7503 7.77772 17.5256C6.43365 17.1386 5.2767 16.2751 4.52349 15.0966C4.08598 14.412 3.83637 13.5702 3.42921 12.1969L2.40431 8.74199C2.06357 7.59338 1.77073 6.60621 1.54431 5.75001H1C0.585786 5.75001 0.25 5.41422 0.25 5.00001C0.25 4.5858 0.585786 4.25001 1 4.25001H3.93198L7.71231 0.469685C8.0052 0.176792 8.48008 0.176792 8.77297 0.469685C9.06586 0.762578 9.06586 1.23745 8.77297 1.53035L6.05331 4.25001H16.1894L13.4697 1.53033C13.1768 1.23744 13.1768 0.762563 13.4697 0.46967ZM9.75 9.00002C9.75 8.5858 9.41421 8.25002 9 8.25002C8.58579 8.25002 8.25 8.5858 8.25 9.00002V13C8.25 13.4142 8.58579 13.75 9 13.75C9.41421 13.75 9.75 13.4142 9.75 13V9.00002ZM13.75 9.00002C13.75 8.5858 13.4142 8.25002 13 8.25002C12.5858 8.25002 12.25 8.5858 12.25 9.00002V13C12.25 13.4142 12.5858 13.75 13 13.75C13.4142 13.75 13.75 13.4142 13.75 13V9.00002Z"/>
                                        </svg>
                                    @endif
                                </span>
                                <span class="cart__text">{{ __('text.common_cart_text_d2') }}</span>
                                <span class="cart__total">{{ $Currency::convert($cart_total) }}</span>
                            @endif
                        </a>
                    </div>
                </div>
                <div class="header__bottom">
                    <div class="header__categories categories" data-da=".fixed-bar, 600, 1">
                        <button class="button-categories icon-menu" type="button">
                            <span class="button-categories__menu">
                            </span>
                            <span class="button-categories__text">{{ __('text.common_categories_menu') }}</span>
                        </button>
                        <div data-tabs class="categories__tabs tabs">
                            <nav data-tabs-titles class="tabs__navigation">
                                <button type="button"
                                    class="tabs__title _tab-active">{{ __('text.common_best_selling_title') }}</button>
                                @foreach ($menu as $category)
                                    <button type="button" class="tabs__title">{{ $category['name'] }}</button>
                                @endforeach
                            </nav>
                            <div data-tabs-body class="tabs__content">
                                <ul class="tabs__body">
                                    @foreach ($bestsellers as $bestseller)
                                        <li class="tabs__item">
                                            <a
                                                href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                @foreach ($menu as $category)
                                    <ul class="tabs__body">
                                        @foreach ($category['products'] as $item)
                                            <li class="tabs__item">
                                                <a
                                                    href="{{ route('home.product', $item['url']) }}">{{ $item['name'] }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="header__menu menu">
                        <nav class="menu__body">
                            <ul class="menu__list">
                                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                    <li class="menu__item"><a class="menu__link"
                                        href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item"><a class="menu__link"
                                            href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item"><a class="menu__link"
                                            href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 900, first"><a class="menu__link"
                                            href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 950, first"><a class="menu__link"
                                            href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 1000, first"><a class="menu__link"
                                            href="{{ route('home.bonus_referral_program', '_' . $domainWithoutZone) }}">{{ __('text.bonus_ref_menu') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 1050, first"><a class="menu__link"
                                            href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 1100, first"><a class="menu__link"
                                            href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                    </li>
                                @else
                                    <li class="menu__item"><a class="menu__link"
                                            href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item"><a class="menu__link"
                                            href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item"><a class="menu__link"
                                            href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 900, first"><a class="menu__link"
                                            href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 950, first"><a class="menu__link"
                                            href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 1000, first"><a class="menu__link"
                                            href="{{ route('home.bonus_referral_program', '') }}">{{ __('text.bonus_ref_menu') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 1050, first"><a class="menu__link"
                                            href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 1100, first"><a class="menu__link"
                                            href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                    </li>
                                @endif
                                <li class="menu__dotts">
                                    <span></span>
                                    <ul class="menu__subslist">

                                    </ul>
                                </li>
                            </ul>
                        </nav>
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
                        <div class="popup_head">{{ __('text.common_push_head') }}</div>
                        <div class="popup_push_text">{{ __('text.common_push_text') }}</div>
                        <div class="push_buttons">
                            <div class="push_decline">{{ __('text.common_decline') }}</div>
                            <div class="push_allow">{{ __('text.common_allow') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="top_block first_block">
                <div class="left_top_block">
                    <div class="discounts_info_block">
                        <div class="num_block">
                            <div class="block_stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="98"
                                    height="18" alt="">
                            </div>
                            <div class="block_num">
                                1 000 000
                            </div>
                            <div class="num_text">
                                {{ __('text.common_customers') }}
                            </div>
                        </div>
                        <div class="discounts_block" data-da=".verified_info_block, 950, last">
                            <div class="discount_block">
                                <div class="discount_top">
                                    <div>
                                        <img src="{{ asset("$design/images/icons/pref-05.svg") }}" alt="">
                                    </div>
                                    <div class="discount_label">{{ __('text.common_save') }}</div>
                                </div>
                                <div class="discount_text">{{ __('text.common_discount') }}</div>
                            </div>
                            <div class="discount_block">
                                <div class="discount_top">
                                    <div>
                                        <img src="{{ asset("$design/images/icons/pref-03.svg") }}" alt="">
                                    </div>
                                    <div class="discount_label">{{ __('text.common_delivery') }}</div>
                                </div>
                                <div class="discount_text">{{ __('text.common_receive') }}</div>
                            </div>
                            <div class="discount_block">
                                <div class="discount_top">
                                    <div>
                                        <img src="{{ asset("$design/images/icons/pref-02.svg") }}" alt="">
                                    </div>
                                    <div class="discount_label">{{ __('text.common_prescription') }}</div>
                                </div>
                                <div class="discount_text">{{ __('text.common_restrictions') }}</div>
                            </div>
                            <div class="discount_block">
                                <div class="discount_top">
                                    <div>
                                        <img src="{{ asset("$design/images/icons/pref-04.svg") }}" alt="">
                                    </div>
                                    <div class="discount_label">{{ __('text.common_moneyback') }}</div>
                                </div>
                                <div class="discount_text">{{ __('text.common_refund') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="white_line"></div>
                    <div class="verified_info_block">
                        <div class="verified_imgs" data-da=".discounts_info_block, 950, last">
                            <div>
                                <img src="{{ asset("$design/images/icons/verified.svg") }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' verified pharmacy' }}" @else alt="" @endif>
                            </div>
                            <div>
                                <img src="{{ asset("$design/images/icons/partners.svg") }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' partners' }}" @else alt="" @endif
                                    class="img_support_first">
                                <img src="{{ asset("$design/images/icons/partners_small.svg") }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' partners' }}" @else alt="" @endif
                                    class="img_support_second">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right_top_block">
                    <img src="{{ asset("$design/images/hero/01.png") }}" alt="">
                </div>
            </div>
            <div class="top_block second_block">
                <div class="num_block">
                    <div class="block_stars">
                        <img src="{{ asset("$design/images/icons/stars.svg") }}" width="98" height="18"
                            alt="">
                    </div>
                    <div class="block_num">
                        1 000 000
                    </div>
                    <div class="num_text">
                        {{ __('text.common_customers') }}
                    </div>
                </div>
                <div class="right_top_block">
                    <img src="{{ asset("$design/images/hero/01.png") }}" alt="">
                </div>
            </div>
            <div class="top_block third_block">
                <div class="verified_info_block">
                    <div class="verified_imgs">
                        <div>
                            <img src="{{ asset("$design/images/icons/verified.svg") }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' verified pharmacy' }}" @else alt="" @endif>
                        </div>
                        <div>
                            <img src="{{ asset("$design/images/icons/partners_small.svg") }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' partners' }}" @else alt="" @endif
                                class="img_support_second">
                        </div>
                    </div>
                </div>
                <div class="white_line"></div>
                <div class="discounts_block">
                    <div class="discount_line">
                        <div class="discount_block">
                            <div class="discount_top">
                                <div>
                                    <img src="{{ asset("$design/images/icons/pref-05.svg") }}" alt="">
                                </div>
                                <div class="discount_label">{{ __('text.common_save') }}</div>
                            </div>
                            <div class="discount_text">{{ __('text.common_discount') }}</div>
                        </div>
                        <div class="discount_block">
                            <div class="discount_top">
                                <div>
                                    <img src="{{ asset("$design/images/icons/pref-02.svg") }}" alt="">
                                </div>
                                <div class="discount_label">{{ __('text.common_prescription') }}</div>
                            </div>
                            <div class="discount_text">{{ __('text.common_restrictions') }}</div>
                        </div>
                    </div>
                    <div class="discount_line">
                        <div class="discount_block">
                            <div class="discount_top">
                                <div>
                                    <img src="{{ asset("$design/images/icons/pref-03.svg") }}" alt="">
                                </div>
                                <div class="discount_label">{{ __('text.common_delivery') }}</div>
                            </div>
                            <div class="discount_text">{{ __('text.common_receive') }}</div>
                        </div>
                        <div class="discount_block">
                            <div class="discount_top">
                                <div>
                                    <img src="{{ asset("$design/images/icons/pref-04.svg") }}" alt="">
                                </div>
                                <div class="discount_label">{{ __('text.common_moneyback') }}</div>
                            </div>
                            <div class="discount_text">{{ __('text.common_refund') }}</div>
                        </div>
                    </div>
                </div>
            </div>

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
            <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_big.png") }}">
        </div>

        </header>

        @yield('content')

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

        <div class="subscribe_body">
            <div class="left_block">
                <div class="subscribe_img">
                    <img src="{{ asset("$design/images/icons/subscribe.svg") }}">
                </div>
                <div class="text_subscribe">
                    <span class="top_text">{{ __('text.common_subscribe') }}</span>
                    <span class="bottom_text">{{ __('text.common_spec_offer') }}</span>
                </div>
            </div>
            <div class="right_block">
                <input type="text" placeholder="Email" class="form__input input" id="email_sub">
                <div class="button_sub">
                    <img src="{{ asset("$design/images/icons/subscribe_mini.svg") }}" class="sub_mini">
                    <span class="button_text">{{ __('text.common_subscribe') }}</span>
                </div>
            </div>
        </div>

        @yield('testimonial', '')

        <footer class="footer">
            <div class="footer_container">
                <div class="footer_left">
                    <div>
                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                            <img src="{{ asset("$design/images/logo_bottom.svg") }}" alt="{{ $domainWithoutZone }}">
                        @else
                            <img src="{{ asset("$design/images/logo_bottom.svg") }}" alt="Logo">
                        @endif
                    </div>
                    <div class="footer_copyright">
                        <p>
                            {{ __('text.license_text_license1_1') }}
                            {{ $domain }}
                            {{ __('text.license_text_license1_2') }}
                            {{ __('text.license_text_license2_d10') }}
                        </p>
                    </div>
                </div>
                <div class="footer_right" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) style="flex-direction: column; gap: 20px;" @endif>
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <div class="footer_right_block">
                            <ul class="footer__menu">
                                <li class="footer__item"><a
                                        href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.bonus_referral_program', '_' . $domainWithoutZone) }}">{{ __('text.bonus_ref_menu') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                </li>
                            </ul>
                            <a href="{{ route('home.affiliate', '_' . $domainWithoutZone) }}" class="footer__button">{{ __('text.common_affiliate_main_menu_button') }}</a>
                        </div>
                    @else
                        <div class="footer_right_block">
                            <ul class="footer__menu">
                                <li class="footer__item"><a
                                        href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.bonus_referral_program', '') }}">{{ __('text.bonus_ref_menu') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                </li>
                            </ul>
                            <a href="{{ route('home.affiliate', '') }}" class="footer__button">{{ __('text.common_affiliate_main_menu_button') }}</a>
                        </div>
                    @endif
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <div class="sitemap_menu">
                            <a class="navigation__link" href="{{ route('home.sitemap', '_' . $domainWithoutZone) }}">{{__('text.menu_title_sitemap')}}</a>
                        </div>
                    @endif
                </div>
                <div class="footer_copyright bottom_license">
                    <p>
                        {{ __('text.license_text_license1_1') }}
                        {{ $domain }}
                        {{ __('text.license_text_license1_2') }}
                        {{ __('text.license_text_license2_d10') }}
                    </p>
                </div>
            </div>
            <div class="social-share">
                <ul>
                    <i class="fa-brands fa-facebook"></i>
                    <i class="fa-brands fa-twitter"></i>
                    <i class="fa-brands fa-telegram"></i>

                </ul>
            </div>
            <div class="fixed-bar">
                <a href="{$path.page}/{$smarty.const.PAGE_MAIN}" class="fixed-bar__item">
                    <div class="fixed-bar__icon fixed-bar__icon--home">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="22" height="20">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-home") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 21" fill="currentColor" width="22" height="20">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.54133 0.490966C9.9609 0.172435 10.4732 0 11 0C11.5268 0 12.0391 0.172435 12.4587 0.490966L13.5201 1.29669C16.6383 3.66551 19.3095 6.57088 21.4085 9.87665L21.7495 10.4122C21.9038 10.6553 21.9901 10.9355 21.9992 11.2234C22.0083 11.5112 21.9399 11.7963 21.8012 12.0487C21.6625 12.3011 21.4586 12.5116 21.2107 12.6583C20.9628 12.805 20.6801 12.8824 20.3921 12.8824H19.2808C19.3451 14.362 19.2647 15.8432 19.0396 17.3083C18.9312 18.0135 18.5738 18.6565 18.0321 19.1209C17.4904 19.5853 16.8003 19.8403 16.0869 19.8397H13.0103V14.5389C13.0103 14.0058 12.7985 13.4944 12.4215 13.1174C12.0445 12.7404 11.5332 12.5286 11 12.5286C10.4668 12.5286 9.95551 12.7404 9.5785 13.1174C9.2015 13.4944 8.9897 14.0058 8.9897 14.5389V19.8397H5.91314C5.20003 19.8396 4.5105 19.5843 3.96924 19.12C3.42797 18.6557 3.0707 18.0131 2.96202 17.3083C2.73687 15.8432 2.65646 14.3604 2.72079 12.8824H1.60789C1.31987 12.8824 1.03716 12.805 0.789281 12.6583C0.541405 12.5116 0.337464 12.3011 0.198765 12.0487C0.0600652 11.7963 -0.00830249 11.5112 0.000804304 11.2234C0.0099111 10.9355 0.0961582 10.6553 0.250535 10.4122L0.589873 9.87504C2.68957 6.56972 5.36131 3.66489 8.47989 1.29669L9.54133 0.490966Z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="fixed-bar__label">{{ __('text.common_home_main_menu_item') }}</div>
                </a>
            </div>
        </footer>
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
    </script>

    <script defer src="{{ asset_ver("$design/js/app.js") }}"></script>
    <script defer src="{{ asset_ver('js/all_js.js') }}"></script>

    @if ($web_statistic)
        <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
    @endif
</body>

</html>
