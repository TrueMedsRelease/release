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
    <meta name="theme-color" content="#ff746c" />
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
    <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">

    @if (env('APP_PWA', 0))
        <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
        <script defer type="text/javascript" src="{{ asset_ver("js/sw-setup.js") }}"></script>
    @endif

    {{-- <script type="text/javascript" src="{{ asset("js/delete_cache.js") }}"></script> --}}

    {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

    <link href="{{ asset($design . '/vendor/custom-select/custom-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset($design . '/vendor/intl-tel/css/intlTelInput.min.css') }}" rel="stylesheet">
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

<body class="no-js webp">
    <script>
        let flagc = false;
        let flagp = false;
        const design = 10;
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

    {{-- <div class="christmas" style="display: none">
        <img loading="lazy" src="{{ asset("pub_images/pay_big.png") }}">
        <img loading="lazy" src="{{ asset("pub_images/christmas_big.png") }}">
        <img loading="lazy" src="{{ asset("pub_images/black_friday_big.png") }}">
        <img loading="lazy" src="{{ asset("pub_images/new_year_big.png") }}">
        <img loading="lazy" src="{{ asset("pub_images/valentine_day_big.png") }}">
    </div> --}}

    <header class="header">
        <div class="container">
            <div class="header__grid-wrapper"><!-- Phones-->
                <address class="header__phones">
                    <div class="header__phones-caption"><button class="link-button"
                            data-dialog="call">{{ __('text.common_callback') }}</button> <span
                            class="caption-text">{{ __('text.common_call_us_top') }}</span></div>
                    <ul class="header__phones-wrapper">
                        @foreach ($phone_arr as $id_phone => $phones)
                            <li class="dropdown-item"><a class="header-phone" href="tel:{{ __('text.phones_title_phone_' . $id_phone) }}">{{ __('text.phones_title_phone_' . $id_phone . '_code') }} {{ __('text.phones_title_phone_' . $id_phone) }}</a></li>
                        @endforeach
                    </ul>
                    <div class="dropdown"><button class="dropdown__button" aria-label="Show dropdown"><span
                                class="icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg#dots-vertical") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 18" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path d="M2.00002 3.00002C2.82845 3.00002 3.50003 2.32845 3.50003 1.50001C3.50003 0.671578 2.82845 0 2.00002 0C1.17158 0 0.5 0.671578 0.5 1.50001C0.5 2.32845 1.17158 3.00002 2.00002 3.00002Z" fill="currentColor"/>
                                        <path d="M2.00002 10.5C2.82845 10.5 3.50003 9.82845 3.50003 9.00002C3.50003 8.17158 2.82845 7.5 2.00002 7.5C1.17158 7.5 0.5 8.17158 0.5 9.00002C0.5 9.82845 1.17158 10.5 2.00002 10.5Z" fill="currentColor"/>
                                        <path d="M2.00002 18C2.82845 18 3.50003 17.3285 3.50003 16.5C3.50003 15.6716 2.82845 15 2.00002 15C1.17158 15 0.5 15.6716 0.5 16.5C0.5 17.3285 1.17158 18 2.00002 18Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span></button>
                        <ul class="dropdown-list"></ul>
                    </div>
                </address><!-- Settings & auth-->
                <div class="header__controls">
                    @if (count($Language::GetAllLanuages()) > 1)
                        <div class="header__lang header-select-wrapper">
                            <select class="header-select" id="lang_select"
                                onchange="location.href=this.options[this.selectedIndex].value">
                                @foreach ($Language::GetAllLanuages() as $item)
                                    <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}"
                                        @if (App::currentLocale() == $item['code']) selected @endif> {{ $item['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="icon header-select-wrapper__icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg#world") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path d="M9.0833 13.5668H7.19135C6.91207 13.5677 6.63539 13.513 6.37737 13.4059C6.11935 13.2988 5.88512 13.1414 5.68827 12.9428L2.03683 9.28369C2.01629 9.52645 2 9.76992 2 10.0177C2.00311 12.1523 2.80533 14.2081 4.24806 15.7786C5.69078 17.3491 7.669 18.32 9.79163 18.4994V14.2766C9.79163 14.0884 9.717 13.9078 9.58416 13.7747C9.45132 13.6416 9.27116 13.5668 9.0833 13.5668Z" fill="currentColor"/>
                                        <path d="M16.4755 3.96875L15.2076 5.23934C14.8749 5.57089 14.4251 5.7576 13.9559 5.75893H12.9792C12.8852 5.75893 12.7951 5.79633 12.7287 5.86288C12.6623 5.92944 12.625 6.01972 12.625 6.11385V6.82367C12.625 7.29432 12.4384 7.74569 12.1063 8.07848C11.7742 8.41128 11.3238 8.59824 10.8542 8.59824C10.7602 8.59824 10.6702 8.63563 10.6037 8.70219C10.5373 8.76875 10.5 8.85903 10.5 8.95315V9.30807C10.5 9.49633 10.5746 9.67687 10.7075 9.80999C10.8403 9.94311 11.0205 10.0179 11.2083 10.0179H13.3333C13.8969 10.0179 14.4374 10.2422 14.8359 10.6416C15.2344 11.041 15.4583 11.5826 15.4583 12.1474V12.8302C15.4583 12.9242 15.4955 13.0143 15.5617 13.0808L17.4211 14.9448C18.5932 13.302 19.142 11.294 18.9687 9.28198C18.7953 7.26994 17.9112 5.3859 16.4755 3.96875Z" fill="currentColor"/>
                                        <path d="M14.0416 12.8306V12.1478C14.0416 11.9595 13.9669 11.779 13.8341 11.6458C13.7013 11.5127 13.5211 11.4379 13.3332 11.4379H11.2083C10.6447 11.4379 10.1042 11.2136 9.70566 10.8142C9.30714 10.4149 9.08326 9.87323 9.08326 9.30845V8.95354C9.08326 8.4829 9.26983 8.03153 9.60192 7.69873C9.93402 7.36594 10.3844 7.17897 10.8541 7.17897C10.948 7.17897 11.0381 7.14158 11.1045 7.07502C11.1709 7.00846 11.2083 6.91819 11.2083 6.82406V6.11423C11.2083 5.64359 11.3948 5.19222 11.7269 4.85943C12.059 4.52663 12.5094 4.33967 12.9791 4.33967H13.9559C14.0495 4.33911 14.1392 4.30192 14.2059 4.23603L15.3824 3.05701C14.2809 2.27739 13.0098 1.77221 11.6745 1.58338C10.3392 1.39455 8.97818 1.52753 7.70441 1.97127C6.43065 2.41501 5.28082 3.15672 4.35034 4.13487C3.41985 5.11303 2.73552 6.29943 2.35413 7.59564L6.68911 11.9398C6.75503 12.0059 6.83333 12.0583 6.91951 12.094C7.0057 12.1296 7.09806 12.1479 7.19131 12.1478H9.08326C9.64684 12.1478 10.1873 12.3721 10.5859 12.7715C10.9844 13.1708 11.2083 13.7125 11.2083 14.2772V18.5C13.2105 18.3349 15.0891 17.4622 16.5087 16.0376L14.5601 14.0849C14.2292 13.7515 14.0429 13.3008 14.0416 12.8306Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="icon header-select-wrapper__chevron">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg#chevron-down") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path d="M9.47256 3.1972C9.41058 3.13471 9.33685 3.08512 9.25561 3.05127C9.17437 3.01743 9.08723 3 8.99923 3C8.91122 3 8.82408 3.01743 8.74284 3.05127C8.6616 3.08512 8.58787 3.13471 8.52589 3.1972L5.47256 6.25053C5.41058 6.31301 5.33685 6.36261 5.25561 6.39645C5.17437 6.4303 5.08723 6.44772 4.99923 6.44772C4.91122 6.44772 4.82408 6.4303 4.74284 6.39645C4.6616 6.36261 4.58787 6.31301 4.52589 6.25053L1.47256 3.1972C1.41058 3.13471 1.33685 3.08512 1.25561 3.05127C1.17437 3.01743 1.08723 3 0.999226 3C0.911218 3 0.824081 3.01743 0.742842 3.05127C0.661602 3.08512 0.587868 3.13471 0.525893 3.1972C0.401726 3.32211 0.332031 3.49107 0.332031 3.6672C0.332031 3.84332 0.401726 4.01229 0.525893 4.1372L3.58589 7.19719C3.96089 7.57172 4.46922 7.7821 4.99923 7.7821C5.52923 7.7821 6.03756 7.57172 6.41256 7.19719L9.47256 4.1372C9.59673 4.01229 9.66642 3.84332 9.66642 3.6672C9.66642 3.49107 9.59673 3.32211 9.47256 3.1972Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                        </div>
                    @endif
                    @if (count($Currency::GetAllCurrency()) > 1)
                        <div class="header__currency header-select-wrapper">
                            <select class="header-select" id="curr_select"
                                onchange="location.href=this.options[this.selectedIndex].value">
                                @foreach ($Currency::GetAllCurrency() as $item)
                                    <option value="{{ route('home.currency', $item['code']) }}"
                                        @if (session('currency') == $item['code']) selected @endif>
                                        {{ Str::upper($item['code']) }} </option>
                                @endforeach
                            </select>
                            <span class="icon header-select-wrapper__icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg#money") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path d="M15.8333 3.33362H4.16667C3.062 3.33494 2.00296 3.77435 1.22185 4.55547C0.440735 5.33658 0.00132321 6.39562 0 7.50029L0 12.5003C0.00132321 13.6049 0.440735 14.664 1.22185 15.4451C2.00296 16.2262 3.062 16.6656 4.16667 16.667H15.8333C16.938 16.6656 17.997 16.2262 18.7782 15.4451C19.5593 14.664 19.9987 13.6049 20 12.5003V7.50029C19.9987 6.39562 19.5593 5.33658 18.7782 4.55547C17.997 3.77435 16.938 3.33494 15.8333 3.33362V3.33362ZM3.33333 14.167C3.16852 14.167 3.0074 14.1181 2.87036 14.0265C2.73332 13.9349 2.62651 13.8048 2.56343 13.6525C2.50036 13.5002 2.48386 13.3327 2.51601 13.171C2.54817 13.0094 2.62753 12.8609 2.74408 12.7444C2.86062 12.6278 3.00911 12.5485 3.17076 12.5163C3.33241 12.4841 3.49996 12.5006 3.65224 12.5637C3.80451 12.6268 3.93466 12.7336 4.02622 12.8706C4.11779 13.0077 4.16667 13.1688 4.16667 13.3336C4.16667 13.5546 4.07887 13.7666 3.92259 13.9229C3.76631 14.0792 3.55435 14.167 3.33333 14.167ZM3.33333 7.50029C3.16852 7.50029 3.0074 7.45141 2.87036 7.35984C2.73332 7.26828 2.62651 7.13813 2.56343 6.98585C2.50036 6.83358 2.48386 6.66603 2.51601 6.50438C2.54817 6.34273 2.62753 6.19424 2.74408 6.0777C2.86062 5.96115 3.00911 5.88178 3.17076 5.84963C3.33241 5.81748 3.49996 5.83398 3.65224 5.89705C3.80451 5.96012 3.93466 6.06694 4.02622 6.20398C4.11779 6.34102 4.16667 6.50213 4.16667 6.66695C4.16667 6.88797 4.07887 7.09993 3.92259 7.25621C3.76631 7.41249 3.55435 7.50029 3.33333 7.50029ZM10 13.3336C9.34073 13.3336 8.69626 13.1381 8.1481 12.7719C7.59994 12.4056 7.17269 11.885 6.9204 11.2759C6.66811 10.6668 6.6021 9.99659 6.73072 9.34998C6.85933 8.70338 7.1768 8.10944 7.64298 7.64326C8.10915 7.17709 8.7031 6.85962 9.3497 6.731C9.9963 6.60238 10.6665 6.66839 11.2756 6.92069C11.8847 7.17298 12.4053 7.60022 12.7716 8.14838C13.1378 8.69655 13.3333 9.34101 13.3333 10.0003C13.3333 10.8843 12.9821 11.7322 12.357 12.3573C11.7319 12.9824 10.8841 13.3336 10 13.3336ZM16.6667 14.167C16.5018 14.167 16.3407 14.1181 16.2037 14.0265C16.0666 13.9349 15.9598 13.8048 15.8968 13.6525C15.8337 13.5002 15.8172 13.3327 15.8493 13.171C15.8815 13.0094 15.9609 12.8609 16.0774 12.7444C16.194 12.6278 16.3424 12.5485 16.5041 12.5163C16.6657 12.4841 16.8333 12.5006 16.9856 12.5637C17.1378 12.6268 17.268 12.7336 17.3596 12.8706C17.4511 13.0077 17.5 13.1688 17.5 13.3336C17.5 13.5546 17.4122 13.7666 17.2559 13.9229C17.0996 14.0792 16.8877 14.167 16.6667 14.167ZM16.6667 7.50029C16.5018 7.50029 16.3407 7.45141 16.2037 7.35984C16.0666 7.26828 15.9598 7.13813 15.8968 6.98585C15.8337 6.83358 15.8172 6.66603 15.8493 6.50438C15.8815 6.34273 15.9609 6.19424 16.0774 6.0777C16.194 5.96115 16.3424 5.88178 16.5041 5.84963C16.6657 5.81748 16.8333 5.83398 16.9856 5.89705C17.1378 5.96012 17.268 6.06694 17.3596 6.20398C17.4511 6.34102 17.5 6.50213 17.5 6.66695C17.5 6.88797 17.4122 7.09993 17.2559 7.25621C17.0996 7.41249 16.8877 7.50029 16.6667 7.50029ZM11.6667 10.0003C11.6667 10.3299 11.5689 10.6522 11.3858 10.9262C11.2026 11.2003 10.9423 11.4139 10.6378 11.5401C10.3333 11.6662 9.99815 11.6992 9.67485 11.6349C9.35155 11.5706 9.05458 11.4119 8.82149 11.1788C8.5884 10.9457 8.42967 10.6487 8.36536 10.3254C8.30105 10.0021 8.33405 9.66702 8.4602 9.36248C8.58635 9.05794 8.79997 8.79764 9.07405 8.6145C9.34813 8.43137 9.67036 8.33362 10 8.33362C10.442 8.33362 10.866 8.50921 11.1785 8.82177C11.4911 9.13433 11.6667 9.55826 11.6667 10.0003Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="icon header-select-wrapper__chevron">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg#chevron-down") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path d="M9.47256 3.1972C9.41058 3.13471 9.33685 3.08512 9.25561 3.05127C9.17437 3.01743 9.08723 3 8.99923 3C8.91122 3 8.82408 3.01743 8.74284 3.05127C8.6616 3.08512 8.58787 3.13471 8.52589 3.1972L5.47256 6.25053C5.41058 6.31301 5.33685 6.36261 5.25561 6.39645C5.17437 6.4303 5.08723 6.44772 4.99923 6.44772C4.91122 6.44772 4.82408 6.4303 4.74284 6.39645C4.6616 6.36261 4.58787 6.31301 4.52589 6.25053L1.47256 3.1972C1.41058 3.13471 1.33685 3.08512 1.25561 3.05127C1.17437 3.01743 1.08723 3 0.999226 3C0.911218 3 0.824081 3.01743 0.742842 3.05127C0.661602 3.08512 0.587868 3.13471 0.525893 3.1972C0.401726 3.32211 0.332031 3.49107 0.332031 3.6672C0.332031 3.84332 0.401726 4.01229 0.525893 4.1372L3.58589 7.19719C3.96089 7.57172 4.46922 7.7821 4.99923 7.7821C5.52923 7.7821 6.03756 7.57172 6.41256 7.19719L9.47256 4.1372C9.59673 4.01229 9.66642 3.84332 9.66642 3.6672C9.66642 3.49107 9.59673 3.32211 9.47256 3.1972Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                        </div>
                    @endif
                    <a class="header__auth" href="{{ route('home.login') }}" target="_blank">
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg#profile") }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="1em" height="1em" fill="currentColor">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0377 1.875C11.5682 1.87499 12.7678 1.87499 13.7196 1.97811C14.6909 2.08334 15.4779 2.30194 16.1498 2.79013C16.5566 3.08567 16.9143 3.44341 17.2099 3.8502C17.6981 4.52213 17.9167 5.30912 18.0219 6.2804C18.125 7.23216 18.125 8.4318 18.125 9.96221V10.0377C18.125 11.5681 18.125 12.7678 18.0219 13.7196C17.9167 14.6909 17.6981 15.4779 17.2099 16.1498C16.9286 16.537 16.591 16.8797 16.2083 17.1667C16.2001 17.1728 16.1919 17.179 16.1837 17.1851C16.1748 17.1916 16.166 17.1981 16.1572 17.2045L16.1498 17.2099C15.4779 17.6981 14.6909 17.9167 13.7196 18.0219C12.7678 18.125 11.5682 18.125 10.0378 18.125H9.96227C8.43187 18.125 7.23216 18.125 6.2804 18.0219C5.30912 17.9167 4.52213 17.6981 3.8502 17.2099C3.83168 17.1964 3.81327 17.1828 3.79497 17.1691C3.41098 16.8817 3.07223 16.5381 2.79013 16.1498C2.30194 15.4779 2.08334 14.6909 1.97811 13.7196C1.87499 12.7678 1.87499 11.5682 1.875 10.0377V9.96229C1.87499 8.43185 1.87499 7.23218 1.97811 6.2804C2.08334 5.30912 2.30194 4.52213 2.79013 3.8502C3.08568 3.44341 3.44341 3.08568 3.8502 2.79013C4.52213 2.30194 5.30912 2.08334 6.2804 1.97811C7.23218 1.87499 8.43185 1.87499 9.96229 1.875H10.0377ZM15.7499 15.9224C15.4118 14.4398 14.0851 13.3333 12.5 13.3333H7.5C5.91489 13.3333 4.58819 14.4398 4.25006 15.9224C4.34345 16.0096 4.4416 16.0918 4.54411 16.1685C4.55765 16.1786 4.57125 16.1887 4.58493 16.1986C5.00826 16.5062 5.55011 16.6855 6.41504 16.7792C7.29166 16.8741 8.42369 16.875 10 16.875C11.5763 16.875 12.7083 16.8741 13.585 16.7792C14.4499 16.6855 14.9917 16.5062 15.4151 16.1986C15.4296 16.1881 15.444 16.1774 15.4583 16.1667C15.56 16.0904 15.6573 16.0089 15.7499 15.9224ZM10 3.54167C7.81387 3.54167 6.04167 5.31387 6.04167 7.5C6.04167 9.68613 7.81387 11.4583 10 11.4583C12.1861 11.4583 13.9583 9.68613 13.9583 7.5C13.9583 5.31387 12.1861 3.54167 10 3.54167Z" fill="currentColor"/>
                                </svg>
                            @endif
                        </span>
                        {{ __('text.common_profile') }}
                    </a>
                </div>
                <!-- Logo-->
                <a class="logo" href="{{ route('home.index') }}">
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <div class="logo__image"><img loading="lazy" src="{{ asset("$design/svg/logo.svg") }}" width="40" height="40" alt="{{ $domainWithoutZone }}"></div>
                    @else
                        <div class="logo__image"><img loading="lazy" src="{{ asset("$design/svg/logo.svg") }}" width="40" height="40" alt="Logo"></div>
                    @endif
                    <div class="logo__title">TrueMeds</div>
                    <div class="logo__text">Discount Store. Since 1998</div>
                </a>
                <div class="header__search-wrapper">
                    <!-- Categories button-->
                    <button class="button categories-button">
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg") }}#hotdog"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                    <path d="M9 18H15M5 12H19M9 6H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            @endif
                        </span>
                        <span class="button__text">{{ __('text.common_categories_menu') }}</span>
                    </button>
                    <!-- Search-->
                    <form class="search-form" action="{{ route('search.search_product') }}" method="post">
                        @csrf
                        <div class="search search-bar" style="width: 100%">
                            <label class="form__label form__label--text">
                                <input class="form__text-input input-text" id="autocomplete" type="text"
                                    placeholder="{{ __('text.common_search') }}" name="search_text">
                            </label>
                            <button class="search-button" aria-label="Search" type="submit">
                                <span class="icon">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg") }}#search"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18" fill="none" width="1em" height="1em" fill="currentColor">
                                            <path d="M8.25 15C11.9779 15 15 11.9779 15 8.25C15 4.52208 11.9779 1.5 8.25 1.5C4.52208 1.5 1.5 4.52208 1.5 8.25C1.5 11.9779 4.52208 15 8.25 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M16.5 16.5L13.5 13.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @endif
                                </span>
                            </button>
                        </div>
                    </form>
                    <!-- Cart-->
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
                    <a class="cart-button button button--secondary" href="{{ route('cart.index') }}"
                        data-counter="{{ $cart_count }}">
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg") }}#cart"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="1em" height="1em" fill="currentColor">
                                    <defs>
                                        <clipPath id="cart_clip0">
                                        <rect width="20" height="20" fill="currentColor"/>
                                        </clipPath>
                                    </defs>

                                    <g clip-path="url(#cart_clip0)">
                                        <path d="M18.9275 3.3975C18.6931 3.1162 18.3996 2.88996 18.0679 2.73485C17.7363 2.57973 17.3745 2.49955 17.0083 2.5H3.535L3.5 2.2075C3.42837 1.59951 3.13615 1.03894 2.67874 0.632065C2.22133 0.225186 1.63052 0.000284828 1.01833 0L0.833333 0C0.61232 0 0.400358 0.0877974 0.244078 0.244078C0.0877974 0.400358 0 0.61232 0 0.833333C0 1.05435 0.0877974 1.26631 0.244078 1.42259C0.400358 1.57887 0.61232 1.66667 0.833333 1.66667H1.01833C1.22244 1.66669 1.41945 1.74163 1.57198 1.87726C1.72451 2.0129 1.82195 2.19979 1.84583 2.4025L2.9925 12.1525C3.11154 13.1665 3.59873 14.1015 4.36159 14.78C5.12445 15.4585 6.10988 15.8334 7.13083 15.8333H15.8333C16.0543 15.8333 16.2663 15.7455 16.4226 15.5893C16.5789 15.433 16.6667 15.221 16.6667 15C16.6667 14.779 16.5789 14.567 16.4226 14.4107C16.2663 14.2545 16.0543 14.1667 15.8333 14.1667H7.13083C6.61505 14.1652 6.11233 14.0043 5.69161 13.7059C5.27089 13.4075 4.95276 12.9863 4.78083 12.5H14.7142C15.6911 12.5001 16.6369 12.1569 17.3865 11.5304C18.1361 10.9039 18.6417 10.0339 18.815 9.0725L19.4692 5.44417C19.5345 5.08417 19.5198 4.71422 19.4262 4.36053C19.3326 4.00684 19.1623 3.67806 18.9275 3.3975Z" fill="currentColor"/>
                                        <path d="M5.83329 20.0006C6.75376 20.0006 7.49995 19.2544 7.49995 18.3339C7.49995 17.4134 6.75376 16.6672 5.83329 16.6672C4.91282 16.6672 4.16663 17.4134 4.16663 18.3339C4.16663 19.2544 4.91282 20.0006 5.83329 20.0006Z" fill="currentColor"/>
                                        <path d="M14.1667 20.0006C15.0871 20.0006 15.8333 19.2544 15.8333 18.3339C15.8333 17.4134 15.0871 16.6672 14.1667 16.6672C13.2462 16.6672 12.5 17.4134 12.5 18.3339C12.5 19.2544 13.2462 20.0006 14.1667 20.0006Z" fill="currentColor"/>
                                    </g>
                                </svg>
                            @endif
                        </span>
                        <span class="button__text">{{ __('text.common_cart_text_d2') }}</span>
                        <span class="button__total">{{ $Currency::Convert($cart_total, true) }}</span>
                    </a>
                </div>
            </div>
            <!-- Header info-->
            <div class="header__info">
                <div class="customer-choise">
                    <div class="customer-choise__logo"><img loading="lazy" src="{{ asset("$design/svg/ui/choise.svg") }}"
                            width="42" height="42" alt="Site logo">
                    </div>
                    <div class="customer-choise__counter">1 000 000</div>
                    <div class="customer-choise__text">{{ __('text.common_customers') }}</div>
                </div>
                <div class="header-brands">
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-1-md-45w.webp") }} 1x,{{ asset("$design/images/brands/brand-1-md-91w.webp") }} 2x">
                            <source type="image/jpeg" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-1-md-45w.jpg") }} 1x,{{ asset("$design/images/brands/brand-1-md-91w.jpg") }} 2x">
                            <source type="image/webp"
                                srcset="{{ asset("$design/images/brands/brand-1-31w.webp") }} 1x,{{ asset("$design/images/brands/brand-1-63w.webp") }} 2x">
                            <img loading="lazy" src="{{ asset("$design/images/brands/brand-1-31w.jpg") }}"
                                srcset="{{ asset("$design/images/brands/brand-1-31w.jpg") }} 1x,{{ asset("$design/images/brands/brand-1-63w.jpg") }} 2x"
                                width="32" height="21" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fda' }}" @else alt="Brand" @endif>
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-2-md-40w.webp") }} 1x,{{ asset("$design/images/brands/brand-2-md-80w.webp") }} 2x">
                            <source type="image/jpeg" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-2-md-40w.jpg") }} 1x,{{ asset("$design/images/brands/brand-2-md-80w.jpg") }} 2x">
                            <source type="image/webp"
                                srcset="{{ asset("$design/images/brands/brand-2-32w.webp") }} 1x,{{ asset("$design/images/brands/brand-2-65w.webp") }} 2x">
                            <img loading="lazy" src="{{ asset("$design/images/brands/brand-2-32w.jpg") }}"
                                srcset="{{ asset("$design/images/brands/brand-2-32w.jpg") }} 1x,{{ asset("$design/images/brands/brand-2-65w.jpg") }} 2x"
                                width="33" height="24" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' pgue' }}" @else alt="Brand" @endif>
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-3-md-45w.webp") }} 1x,{{ asset("$design/images/brands/brand-3-md-91w.webp") }} 2x">
                            <source type="image/jpeg" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-3-md-45w.jpg") }} 1x,{{ asset("$design/images/brands/brand-3-md-91w.jpg") }} 2x">
                            <source type="image/webp"
                                srcset="{{ asset("$design/images/brands/brand-3-37w.webp") }} 1x,{{ asset("$design/images/brands/brand-3-75w.webp") }} 2x">
                            <img loading="lazy" src="{{ asset("$design/images/brands/brand-3-37w.jpg") }}"
                                srcset="{{ asset("$design/images/brands/brand-3-37w.jpg") }} 1x,{{ asset("$design/images/brands/brand-3-75w.jpg") }} 2x"
                                width="38" height="24" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @else alt="Brand" @endif>
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-4-md-29w.webp") }} 1x,{{ asset("$design/images/brands/brand-4-md-59w.webp") }} 2x">
                            <source type="image/jpeg" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-4-md-29w.jpg") }} 1x,{{ asset("$design/images/brands/brand-4-md-59w.jpg") }} 2x">
                            <source type="image/webp"
                                srcset="{{ asset("$design/images/brands/brand-4-24w.webp") }} 1x,{{ asset("$design/images/brands/brand-4-49w.webp") }} 2x">
                            <img loading="lazy" src="{{ asset("$design/images/brands/brand-4-24w.jpg") }}"
                                srcset="{{ asset("$design/images/brands/brand-4-24w.jpg") }} 1x,{{ asset("$design/images/brands/brand-4-49w.jpg") }} 2x"
                                width="25" height="24" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cipa' }}" @else alt="Brand" @endif>
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-5-md-53w.webp") }} 1x,{{ asset("$design/images/brands/brand-5-md-106w.webp") }} 2x">
                            <source type="image/jpeg" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-5-md-53w.jpg") }} 1x,{{ asset("$design/images/brands/brand-5-md-106w.jpg") }} 2x">
                            <source type="image/webp"
                                srcset="{{ asset("$design/images/brands/brand-5-42w.webp") }} 1x,{{ asset("$design/images/brands/brand-5-85w.webp") }} 2x">
                            <img loading="lazy" src="{{ asset("$design/images/brands/brand-5-42w.jpg") }}"
                                srcset="{{ asset("$design/images/brands/brand-5-42w.jpg") }} 1x,{{ asset("$design/images/brands/brand-5-85w.jpg") }} 2x"
                                width="43" height="24" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @else alt="Brand" @endif>
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-6-md-72w.webp") }} 1x,{{ asset("$design/images/brands/brand-6-md-144w.webp") }} 2x">
                            <source type="image/jpeg" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-6-md-72w.jpg") }} 1x,{{ asset("$design/images/brands/brand-6-md-144w.jpg") }} 2x">
                            <source type="image/webp"
                                srcset="{{ asset("$design/images/brands/brand-6-55w.webp") }} 1x,{{ asset("$design/images/brands/brand-6-111w.webp") }} 2x">
                            <img loading="lazy" src="{{ asset("$design/images/brands/brand-6-55w.jpg") }}"
                                srcset="{{ asset("$design/images/brands/brand-6-55w.jpg") }} 1x,{{ asset("$design/images/brands/brand-6-111w.jpg") }} 2x"
                                width="56" height="22" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mcafree' }}" @else alt="Brand" @endif>
                        </picture>
                    </div>
                </div>
            </div>
            <nav class="nav header-nav">
                <div class="nav-container">
                    <ul class="nav__list">
                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                            <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.bonus_referral_program', '_' . $domainWithoutZone) }}">{{ __('text.bonus_ref_menu') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a></li>
                        @else
                            <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.bonus_referral_program', '') }}">{{ __('text.bonus_ref_menu') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a></li>
                        @endif
                    </ul>
                </div><button class="greedy-button" aria-label="Show dropdown">
                    <span class="icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg#dots-vertical") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 18" fill="none" width="1em" height="1em" fill="currentColor">
                                <path d="M2.00002 3.00002C2.82845 3.00002 3.50003 2.32845 3.50003 1.50001C3.50003 0.671578 2.82845 0 2.00002 0C1.17158 0 0.5 0.671578 0.5 1.50001C0.5 2.32845 1.17158 3.00002 2.00002 3.00002Z" fill="currentColor"/>
                                <path d="M2.00002 10.5C2.82845 10.5 3.50003 9.82845 3.50003 9.00002C3.50003 8.17158 2.82845 7.5 2.00002 7.5C1.17158 7.5 0.5 8.17158 0.5 9.00002C0.5 9.82845 1.17158 10.5 2.00002 10.5Z" fill="currentColor"/>
                                <path d="M2.00002 18C2.82845 18 3.50003 17.3285 3.50003 16.5C3.50003 15.6716 2.82845 15 2.00002 15C1.17158 15 0.5 15.6716 0.5 16.5C0.5 17.3285 1.17158 18 2.00002 18Z" fill="currentColor"/>
                            </svg>
                        @endif
                    </span></button>
                <ul class="hidden-links dropdown-list"></ul>
            </nav>
            <nav class="nav cat-nav">
                <div class="nav-container">
                    <div class="nav__heading">{{ __('text.common_categories_menu') }}</div><button
                        class="nav__close-button" aria-label="Close categories"></button>
                    <ul class="nav__list">
                        <li class="nav__item">
                            <a class="nav__link is-active nav__sublist-toggler" href="{{ route('home.index') }}"
                                data-sublist-index="0">{{ __('text.common_best_selling_title') }}</a>
                        </li>
                        @foreach ($menu as $category)
                            <li class="nav__item">
                                <a class="nav__link nav__sublist-toggler"
                                    href="{{ route('home.category', $category['url']) }}"
                                    data-sublist-index="{{ $loop->iteration }}">{{ $category['name'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="categories-sublists">
                        <ul class="nav__sublist sublist-4-col" data-sublist-index="0">
                            <li class="nav__item nav__item--return">
                                <button
                                    class="nav__mobile-return">{{ __('text.common_best_selling_title') }}</button>
                            </li>
                            @foreach ($bestsellers as $bestseller)
                                <li class="nav__item"><a class="nav__link"
                                        href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                        @foreach ($menu as $category)
                            <ul class="nav__sublist sublist-4-col" data-sublist-index="{{ $loop->iteration }}">
                                <li class="nav__item nav__item--return">
                                    <button class="nav__mobile-return">{{ $category['name'] }}</button>
                                </li>
                                @foreach ($category['products'] as $item)
                                    <li class="nav__item"><a class="nav__link"
                                            href="{{ route('home.product', $item['url']) }}">{{ $item['name'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <div class="container sub-header"><!-- Drug index-->
        <div class="drug-index">
            <div class="drug-index__container">
                <ul class="drug-index__list">
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
        </div><!-- Store info-->
        <div class="store-info">
            <div class="store-info-caption">
                <div class="store-info-caption__title">{{ __('text.common_verified') }}</div>
                <div class="store-info-caption__text">{{ __('text.common_approved_d4') }}</div>
            </div>
            <div class="store-info-blocks">
                <div class="store-info-block store-info-block--1">
                    <div class="store-info-block__title">{{ __('text.common_save') }}</div>
                    <div class="store-info-block__text">{{ __('text.common_discount') }}</div>
                </div>
                <div class="store-info-block store-info-block--2">
                    <div class="store-info-block__title">{{ __('text.common_prescription') }}</div>
                    <div class="store-info-block__text">{{ __('text.common_restrictions') }}</div>
                </div>
                <div class="store-info-block store-info-block--3">
                    <div class="store-info-block__title">{{ __('text.common_delivery') }}</div>
                    <div class="store-info-block__text">{{ __('text.common_receive') }}</div>
                </div>
                <div class="store-info-block store-info-block--4">
                    <div class="store-info-block__title">{{ __('text.common_moneyback') }}</div>
                    <div class="store-info-block__text">{{ __('text.common_refund') }}</div>
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
            <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_big_v2.png") }}">
        </div>
    </div>

    @yield('content')

    <div class="container sup-footer">
        <div class="subscribe">
            <div class="subscribe__caption">
                <div class="subscribe__title">{{ __('text.common_subscribe') }}</div>
                <div class="subscribe__text">{{ __('text.common_spec_offer') }}</div>
            </div>
            <form class="form form form--secondary subscribe-form">
                <div class="form__field">
                    <label class="form__label form__label--email">
                        <input class="form__text-input input-email" type="email"
                            placeholder="{{ __('text.affiliate_email') }}" id="email_sub">
                    </label>
                </div>
                <div class="form__field">
                    <div class="button form__submit button_sub">{{ __('text.common_subscribe') }}</div>
                </div>
            </form>
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

        @yield('rewies')
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer__wrapper"><!-- Footer logo--><a class="logo logo--footer" href="{{ route('home.index') }}">
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <div class="logo__image"><img loading="lazy" src="{{ asset("$design/svg/logo-footer.svg") }}" width="40" height="40" alt="{{ $domainWithoutZone }}"></div>
                    @else
                        <div class="logo__image"><img loading="lazy" src="{{ asset("$design/svg/logo-footer.svg") }}" width="40" height="40" alt="Logo"></div>
                    @endif
                    <div class="logo__title">TrueMeds</div>
                    <div class="logo__text">Discount Store. Since 1998</div>
                </a>

                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                    <nav class="nav footer-nav">
                        <div class="nav-container">
                            <ul class="nav__list">
                                <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.bonus_referral_program', '_' . $domainWithoutZone) }}">{{ __('text.bonus_ref_menu') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a></li>
                            </ul>
                        </div>
                    </nav>
                    <a class="button" href="{{ route('home.affiliate', '_' . $domainWithoutZone) }}">{{ __('text.common_affiliate_main_menu_button') }}</a>
                @else
                    <nav class="nav footer-nav">
                        <div class="nav-container">
                            <ul class="nav__list">
                                <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.bonus_referral_program', '') }}">{{ __('text.bonus_ref_menu') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a></li>
                            </ul>
                        </div>
                    </nav>
                    <a class="button" href="{{ route('home.affiliate', '') }}">{{ __('text.common_affiliate_main_menu_button') }}</a>
                @endif
            </div>
            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                <div class="sitemap_menu">
                    <a class="nav__link" href="{{ route('home.sitemap', '_' . $domainWithoutZone) }}">{{__('text.menu_title_sitemap')}}</a>
                </div>
            @endif
            <!-- Copyrights-->
            <div class="footer__copyrights">
                <p>
                    {{ __('text.license_text_license1_1') }}
                    {{ $domain }}
                    {{ __('text.license_text_license1_2') }}
                    {{ __('text.license_text_license2_d10') }}
                </p>
            </div>
            <!-- Footer controls-->
            <div class="footer-buttons">
                <div class="footer-buttons__container">
                    <a class="footer-button" href="{{ route('home.index') }}">
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg#home") }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 24" fill="none" width="1em" height="1em" fill="currentColor">
                                    <path d="M13.0062 14.4949C11.6246 14.4949 10.5046 15.6149 10.5046 16.9964V21.9995H15.5078V16.9964C15.5078 15.6149 14.3878 14.4949 13.0062 14.4949Z" fill="currentColor"/>
                                    <path d="M17.1755 16.9969V22H20.5109C21.8925 22 23.0125 20.88 23.0125 19.4984V11.8929C23.0127 11.4597 22.8443 11.0434 22.543 10.7322L15.4569 3.07153C14.2066 1.71874 12.0964 1.63568 10.7436 2.88599C10.6794 2.9454 10.6174 3.00728 10.5581 3.07153L3.48448 10.7297C3.17405 11.0422 2.99988 11.4649 3 11.9054V19.4984C3 20.88 4.11999 22 5.50156 22H8.83695V16.9969C8.85255 14.7231 10.6883 12.8663 12.9048 12.8129C15.1955 12.7576 17.158 14.646 17.1755 16.9969Z" fill="currentColor"/>
                                    <path d="M13.0062 14.4949C11.6246 14.4949 10.5046 15.6149 10.5046 16.9964V21.9995H15.5078V16.9964C15.5078 15.6149 14.3878 14.4949 13.0062 14.4949Z" fill="currentColor"/>
                                </svg>
                            @endif
                        </span>
                        <span class="button__text">{{ __('text.common_home_main_menu_item') }}</span>
                    </a>
                    <button class="footer-button footer-button--cat">
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg#hotdog") }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                    <path d="M9 18H15M5 12H19M9 6H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            @endif
                        </span>
                        <span class="button__text">{{ __('text.common_categories_menu') }}</span>
                    </button>
                    <a class="footer-button" href="{{ route('home.login') }}" target="_blank">
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg#profile") }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="1em" height="1em" fill="currentColor">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0377 1.875C11.5682 1.87499 12.7678 1.87499 13.7196 1.97811C14.6909 2.08334 15.4779 2.30194 16.1498 2.79013C16.5566 3.08567 16.9143 3.44341 17.2099 3.8502C17.6981 4.52213 17.9167 5.30912 18.0219 6.2804C18.125 7.23216 18.125 8.4318 18.125 9.96221V10.0377C18.125 11.5681 18.125 12.7678 18.0219 13.7196C17.9167 14.6909 17.6981 15.4779 17.2099 16.1498C16.9286 16.537 16.591 16.8797 16.2083 17.1667C16.2001 17.1728 16.1919 17.179 16.1837 17.1851C16.1748 17.1916 16.166 17.1981 16.1572 17.2045L16.1498 17.2099C15.4779 17.6981 14.6909 17.9167 13.7196 18.0219C12.7678 18.125 11.5682 18.125 10.0378 18.125H9.96227C8.43187 18.125 7.23216 18.125 6.2804 18.0219C5.30912 17.9167 4.52213 17.6981 3.8502 17.2099C3.83168 17.1964 3.81327 17.1828 3.79497 17.1691C3.41098 16.8817 3.07223 16.5381 2.79013 16.1498C2.30194 15.4779 2.08334 14.6909 1.97811 13.7196C1.87499 12.7678 1.87499 11.5682 1.875 10.0377V9.96229C1.87499 8.43185 1.87499 7.23218 1.97811 6.2804C2.08334 5.30912 2.30194 4.52213 2.79013 3.8502C3.08568 3.44341 3.44341 3.08568 3.8502 2.79013C4.52213 2.30194 5.30912 2.08334 6.2804 1.97811C7.23218 1.87499 8.43185 1.87499 9.96229 1.875H10.0377ZM15.7499 15.9224C15.4118 14.4398 14.0851 13.3333 12.5 13.3333H7.5C5.91489 13.3333 4.58819 14.4398 4.25006 15.9224C4.34345 16.0096 4.4416 16.0918 4.54411 16.1685C4.55765 16.1786 4.57125 16.1887 4.58493 16.1986C5.00826 16.5062 5.55011 16.6855 6.41504 16.7792C7.29166 16.8741 8.42369 16.875 10 16.875C11.5763 16.875 12.7083 16.8741 13.585 16.7792C14.4499 16.6855 14.9917 16.5062 15.4151 16.1986C15.4296 16.1881 15.444 16.1774 15.4583 16.1667C15.56 16.0904 15.6573 16.0089 15.7499 15.9224ZM10 3.54167C7.81387 3.54167 6.04167 5.31387 6.04167 7.5C6.04167 9.68613 7.81387 11.4583 10 11.4583C12.1861 11.4583 13.9583 9.68613 13.9583 7.5C13.9583 5.31387 12.1861 3.54167 10 3.54167Z" fill="currentColor"/>
                                </svg>
                            @endif
                        </span>
                        <span class="button__text">{{ __('text.common_profile') }}</span>
                    </a>
                    <a class="footer-button footer-button--cart" href="{{ route('cart.index') }}"
                        data-counter="{{ $cart_count }}">
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg#cart") }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="1em" height="1em" fill="currentColor">
                                    <defs>
                                        <clipPath id="cart_clip0">
                                        <rect width="20" height="20" fill="currentColor"/>
                                        </clipPath>
                                    </defs>

                                    <g clip-path="url(#cart_clip0)">
                                        <path d="M18.9275 3.3975C18.6931 3.1162 18.3996 2.88996 18.0679 2.73485C17.7363 2.57973 17.3745 2.49955 17.0083 2.5H3.535L3.5 2.2075C3.42837 1.59951 3.13615 1.03894 2.67874 0.632065C2.22133 0.225186 1.63052 0.000284828 1.01833 0L0.833333 0C0.61232 0 0.400358 0.0877974 0.244078 0.244078C0.0877974 0.400358 0 0.61232 0 0.833333C0 1.05435 0.0877974 1.26631 0.244078 1.42259C0.400358 1.57887 0.61232 1.66667 0.833333 1.66667H1.01833C1.22244 1.66669 1.41945 1.74163 1.57198 1.87726C1.72451 2.0129 1.82195 2.19979 1.84583 2.4025L2.9925 12.1525C3.11154 13.1665 3.59873 14.1015 4.36159 14.78C5.12445 15.4585 6.10988 15.8334 7.13083 15.8333H15.8333C16.0543 15.8333 16.2663 15.7455 16.4226 15.5893C16.5789 15.433 16.6667 15.221 16.6667 15C16.6667 14.779 16.5789 14.567 16.4226 14.4107C16.2663 14.2545 16.0543 14.1667 15.8333 14.1667H7.13083C6.61505 14.1652 6.11233 14.0043 5.69161 13.7059C5.27089 13.4075 4.95276 12.9863 4.78083 12.5H14.7142C15.6911 12.5001 16.6369 12.1569 17.3865 11.5304C18.1361 10.9039 18.6417 10.0339 18.815 9.0725L19.4692 5.44417C19.5345 5.08417 19.5198 4.71422 19.4262 4.36053C19.3326 4.00684 19.1623 3.67806 18.9275 3.3975Z" fill="currentColor"/>
                                        <path d="M5.83329 20.0006C6.75376 20.0006 7.49995 19.2544 7.49995 18.3339C7.49995 17.4134 6.75376 16.6672 5.83329 16.6672C4.91282 16.6672 4.16663 17.4134 4.16663 18.3339C4.16663 19.2544 4.91282 20.0006 5.83329 20.0006Z" fill="currentColor"/>
                                        <path d="M14.1667 20.0006C15.0871 20.0006 15.8333 19.2544 15.8333 18.3339C15.8333 17.4134 15.0871 16.6672 14.1667 16.6672C13.2462 16.6672 12.5 17.4134 12.5 18.3339C12.5 19.2544 13.2462 20.0006 14.1667 20.0006Z" fill="currentColor"/>
                                    </g>
                                </svg>
                            @endif
                        </span>
                        <span class="button__text">{{ $Currency::Convert($cart_total, true) }}</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    <div class="cat-overlay overlay"></div>

    <dialog class="dialog" data-name="call" data-modal="true" data-clickable-backdrop="true">
        <div class="dialog__inner-wrapper">
            <header class="dialog__header">
                <div class="dialog__title">{{ __('text.common_callback') }}</div>
                <button class="dialog__close-button close-button">Close</button>
            </header>
            <form class="form callback-form" method="dialog">
                <div class="form__field phone_block">
                    <div class="enter-info__country phone_code">
                        <select name="phone_code" class="form" id="phone_code_select" data-scroll>
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
                <div class="form__field">
                    <input class="button form__submit button_request_call" type="submit"
                        value="{{ __('text.common_callback') }}">
                </div>
            </form>
            <div class="message_sended hidden">
                <button class="dialog__close-button close-button-message">Close</button>
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

    <div class="popup_gray" style="display: none">
        <div class="popup_call">
            <button class="dialog__close-button close-button-message">Close</button>
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

        const pathImageCheckupBiggest = "{{ asset('pub_images/checkup_img/white/checkup_biggest_v2.png') }}";
        const pathImageCheckupBig = "{{ asset('pub_images/checkup_img/white/checkup_big_v2.png') }}";
        const pathImageCheckupMiddle = "{{ asset('pub_images/checkup_img/white/checkup_middle_v2.png') }}";
        const pathImageCheckupSmall = "{{ asset('pub_images/checkup_img/white/checkup_small_v2.png') }}";

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

    <script defer src="{{ asset_ver($design . "/js/app.js") }}"></script>
    <script defer src="{{ asset($design . '/js/custom-select.min.js') }}"></script>
    <script defer src="{{ asset($design . '/js/intlTelInput.min.js') }}"></script>
    <script defer src="{{ asset_ver($design . '/js/main.js') }}"></script>
    <script defer src="{{ asset_ver('js/all_js.js') }}"></script>
    @if ($web_statistic)
        <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
    @endif
</body>

</html>
