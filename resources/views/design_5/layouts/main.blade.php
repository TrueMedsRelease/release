<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Defult')</title>
    <meta name="description" content="Verified Pharmacy Store">
    <meta name="keywords" content="key, words">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#4FAFCD"/>
	<meta name="format-detection" content="telephone=no">

    <link rel="alternate" href="{{ config('app.url') }}/lang=arb" hreflang="ar" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=cs" hreflang="cs" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=da" hreflang="da" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=de" hreflang="de" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=en" hreflang="en" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=es" hreflang="es" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=fi" hreflang="fi" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=fr" hreflang="fr" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=gr" hreflang="gr" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=hans" hreflang="zh-Hans" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=hant" hreflang="zh-Hant" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=hu" hreflang="hu" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=it" hreflang="it" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=ja" hreflang="ja" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=ms" hreflang="ms" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=nl" hreflang="nl" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=no" hreflang="no" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=pl" hreflang="pl" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=pt" hreflang="pt" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=ro" hreflang="ro" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=sk" hreflang="sk" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=sv" hreflang="sv" />

    <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">
    <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">

    <link href="{{ asset($design . '/css/style.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset($design . '/css/all.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset($design . '/css/intlTelInput.css') }}" rel="stylesheet">
    <link href="{{ asset($design . '/css/slick.css') }}" rel="stylesheet">

    <script src="{{ asset("vendor/jquery/jquery-3.6.3.min.js") }}"></script>
    <script src="{{ asset("vendor/jquery/autocomplete.js") }}"></script>
    <script src="{{ asset("vendor/jquery/init.js") }}"></script>
    <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.js"></script>
</head>
<body>
<script>
    let flagc = false;
    let flagp = false;
    let flagm = false;
    const design = 5;
</script>
<header class="header">
    <div class="phone-box">
        <div class="container">
            <div class="holder-phone">
                <div class="phone_info"><a class="request_call">{{ __('text.common_callback') }}</a><div class="request_text">{{__('text.common_call_us_top')}}</div></div>
                <a href="tel:{{__('text.phones_title_phone_1')}}">{{__('text.phones_title_phone_1_code')}}{{__('text.phones_title_phone_1')}}</a>
                <a href="tel:{{__('text.phones_title_phone_2')}}">{{__('text.phones_title_phone_2_code')}}{{__('text.phones_title_phone_2')}}</a>
                <a href="tel:{{__('text.phones_title_phone_3')}}">{{__('text.phones_title_phone_3_code')}}{{__('text.phones_title_phone_3')}}</a>
                <a href="tel:{{__('text.phones_title_phone_4')}}">{{__('text.phones_title_phone_4_code')}}{{__('text.phones_title_phone_4')}}</a>
                <a href="tel:{{__('text.phones_title_phone_5')}}">{{__('text.phones_title_phone_5_code')}}{{__('text.phones_title_phone_5')}}</a>
                <a href="tel:{{__('text.phones_title_phone_6')}}">{{__('text.phones_title_phone_6_code')}}{{__('text.phones_title_phone_6')}}</a>
                <a href="tel:{{__('text.phones_title_phone_7')}}">{{__('text.phones_title_phone_7_code')}}{{__('text.phones_title_phone_7')}}</a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="nav">
            <span class="close"></span>
            <div class="menu_top">
                <ul>
                    <li class="categories_button"><img src="{{ asset("$design/images/icon/ico-menu.svg") }}" alt=""><a class="categories_a">{{__('text.common_categories_menu')}}</a></li>
                    <li><a href="{{ route('home.index') }}">{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                    <li><a href="{{ route('home.about') }}">{{__('text.common_about_us_main_menu_item')}}</a></li>
                    <li><a href="{{ route('home.help') }}">{{__('text.common_help_main_menu_item')}}</a></li>
                    <li><a href="{{ route('home.testimonials') }}">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                    <li><a href="{{ route('home.delivery') }}">{{__('text.common_shipping_main_menu_item')}}</a></li>
                    <li><a href="{{ route('home.moneyback') }}">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                    <li><a href="{{ route('home.contact_us') }}">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                </ul>
            </div>
            <aside class="categories-sidebar hide">
                <div class="categories-sidebar__inner">
                    <div data-spollers class="categories-sidebar__spollers spollers">
                        <div class="spollers__item">
                            <button type="button" data-spoller class="spollers__title _spoller-active">{{__('text.common_best_selling_title')}}</button>
                            <ul class="spollers__body main_bestsellers" id="main_bestsellers">
                                @foreach ($bestsellers as $bestseller)
                                    <li class="spollers__item-list"><a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span style="font-size: 12px;">{{ $Currency::convert($bestseller['price'], false, true) }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                        @foreach ($menu as $category)
                            {{-- {if $cur_category.name eq $data.product_info.category_name || $cur_category.name eq $data.category_name}
                                <div class="spollers__item">
                                    <button type="button" data-spoller class="spollers__title _spoller-active">{$cur_category.name}</button>
                                    <ul class="spollers__body" id="this_product_category">
                                        {foreach item=cur_product from=$cur_category.products}
                                            <li class="spollers__item-list">
                                                <a href="{$path.page}/{$cur_product.url}">
                                                    {$cur_product.name}
                                                </a>
                                                <span style="font-size: 12px;">{$cur_product.min_price_per_pill}</span>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            {else} --}}
                                <div class="spollers__item">
                                    <button type="button" data-spoller class="spollers__title">{{ $category['name'] }}</button>
                                    <ul class="spollers__body">
                                        @foreach ($category['products'] as $item)
                                            <li class="spollers__item-list">
                                                <a href="{{ route('home.product', $item['url']) }}">
                                                    {{ $item['name'] }}
                                                </a>
                                                <span style="font-size: 12px;">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            {{-- {/if} --}}
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>

        <div class="header-container">
            <div class="panel-box">
                <a href="{{ route('home.index') }}" class="logo">
                    <img src="{{ asset("$design/images/logo.svg") }}" width="145" height="40" alt="">
                </a>
                <div class="drop-info">
                    <div class="lang drop">
                        <select name="form[]" class="form" onchange="location.href=this.options[this.selectedIndex].value">
                            @foreach ($Language::GetAllLanuages() as $language)
                                <option value="/lang={{$language['code']}}" @if (App::currentLocale() == $language['code']) selected @endif>{{$language['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="wallet drop">
                        <select name="form[]" class="form" onchange="location.href=this.options[this.selectedIndex].value">
                            @foreach ($Currency::GetAllCurrency() as $item)
                                <option value="/curr={{ $item['code'] }}" @if (session('currency') == $item['code']) selected @endif> {{ Str::upper($item['code']) }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="profile_top_block">
                        <a href="{{ config('app.url') }}/login" class="item profile_top" target="_blank">
                            <span class="ico">
                              <img src="{{ asset("$design/images/icon/ico-profile.svg") }}" alt="" width="20" height="20">
                            </span>
                            <span class="name">{{__('text.common_profile')}}</span>
                        </a>
                    </div>
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
                <a @if ($cart_count != 0)href="{{ route('cart.index') }}"@endif class="cart-box">
                      <span class="icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 19.1249H8.079C7.7247 19.125 7.3818 18.9996 7.111 18.7709C6.8403 18.5423 6.6593 18.2253 6.6 17.8759L3.963 2.37593C3.9035 2.02678 3.7224 1.70994 3.4517 1.48153C3.181 1.25311 2.8382 1.12785 2.484 1.12793H1.5" stroke="#262D38" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16.125 22.125C16.3321 22.125 16.5 21.9571 16.5 21.75C16.5 21.5429 16.3321 21.375 16.125 21.375" stroke="#262D38" stroke-width="1.5"/>
                        <path d="M16.125 22.125C15.9179 22.125 15.75 21.9571 15.75 21.75C15.75 21.5429 15.9179 21.375 16.125 21.375" stroke="#262D38" stroke-width="1.5"/>
                        <path d="M8.625 22.125C8.8321 22.125 9 21.9571 9 21.75C9 21.5429 8.8321 21.375 8.625 21.375" stroke="#262D38" stroke-width="1.5"/>
                        <path d="M8.625 22.125C8.4179 22.125 8.25 21.9571 8.25 21.75C8.25 21.5429 8.4179 21.375 8.625 21.375" stroke="#262D38" stroke-width="1.5"/>
                        <path d="M6.04661 14.6251H18.1176C18.7865 14.625 19.4362 14.4014 19.9635 13.9897C20.4907 13.5781 20.8653 13.002 21.0276 12.3531L22.4776 6.55309C22.5053 6.44248 22.5073 6.32702 22.4837 6.21548C22.46 6.10394 22.4112 5.99927 22.3411 5.90941C22.2709 5.81955 22.1811 5.74688 22.0786 5.69692C21.9761 5.64696 21.8636 5.62103 21.7496 5.62109H4.51561" stroke="#262D38" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 8.625V10.875" stroke="#262D38" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M13.5 8.625V10.875" stroke="#262D38" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 8.625V10.875" stroke="#262D38" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                            @if ($cart_count != 0)
                                <span class="status">{{$cart_count}}</span>
                            @endif
                      </span>
                    <span class="price">{{$Currency::convert($cart_total)}}</span>
                </a>
            </div>
            <div class="certificates-box">
                <div class="verified-info">
                    <strong>{{__('text.common_verified_d4')}}</strong>
                    <span>{{__('text.common_approved_d4')}}</span>
                </div>
                <img src="{{ asset("$design/images/img-certificates.png") }}" alt="">
            </div>
        </div>
    </div>

    <div class="popup_gray" style="display: none">
        <div class="popup_call">
            <div class="button_close">
                <svg class="close_popup" width="15" height="15">
                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                </svg>
            </div>
            <div class="popup_bottom">
                <div class="popup_text">{{__('text.common_callback')}}</div>
                <div class="phone">
                    <div class="enter-info__country phone_code">
                        <select name="phone_code" class="form" data-scroll>
                            @foreach ($phone_codes as $item)
                                <option id=""
                                @if (empty(session('form')))
                                        @selected($item['iso'] == session('location.country', ''))
                                @else
                                    @selected($item['iso'] == session('form.phone_code', ''))
                                @endif
                                    data-asset="{{ asset('style_checkout/images/countrys/' . $item['nicename'] . '.svg') }}"
                                    value="{{ $item['iso'] }}">
                                    +{{ $item['phonecode'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="enter-info__input enter-info__input--country">
                        <input required autocomplete="off" type="number" id="phone" name="phone" value="{$data.info.phone}" placeholder="000 000 00 00" class="input" maxlength = "14" oninput="maxLengthCheck(this)">
                    </div>
                </div>
                <div class="button_request_call">{{__('text.common_callback')}}</div>
            </div>
            <div class="message_sended request hidden">
                <h2>{{__('text.contact_us_thanks')}}</h2>
                <br>
                <p>{{__('text.phone_request_mes_text')}}</p>
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
                <div class="popup_head">{{__('text.common_push_head')}}</div>
                <div class="popup_push_text">{{__('text.common_push_text')}}</div>
                <div class="push_buttons">
                    <div class="push_decline">{{__('text.common_decline')}}</div>
                    <div class="push_allow">{{__('text.common_allow')}}</div>
                </div>
            </div>
        </div>
    </div>
</header>
<main class="content">
    <div class="container">

        @yield('content')

	</div>
</main>

<section class="subscribe_container">
    <div class="block_subscribe">
        <div class="left_block">
            <div class="subscribe_img">
                <img src="{{ asset("$design/images/icons/subscribe.svg") }}">
            </div>
            <div class="text_subscribe">
                <span class="top_text">{{__('text.common_subscribe')}}</span>
                <span class="bottom_text">{{__('text.common_spec_offer')}}</span>
            </div>
        </div>
        <div class="right_block">
            <input type="text" placeholder="Email" class="form__input input" id="email_sub">
            <div class="button_sub">
                <img src="{{ asset("$design/images/icons/subscribe_mini.svg") }}" class="sub_mini">
                <span class="button_text">{{__('text.common_subscribe')}}</span>
            </div>
        </div>
    </div>
</section>


<section class="ship-index">
    <div class="ship-index__container">
        <ul class="ship-index__list">
            <li class="ship-index__item">
                <img src="/images/shipping/usps.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/ems.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/dhl.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/ups.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/fedex.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/tnt.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/postnl.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/deutsche_post.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/dpd.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/gls.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/australia_post.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/colissimo.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/correos.svg" alt="">
            </li>
        </ul>
    </div>
</section>

<footer class="footer">
    <div class="item-box">
        <section class="page__reviews reviews">
            <div class="feedback">
                <div class="item">
                    <div class="head">
                        <span class="name">{!!__('text.testimonials_author_t_1')!!}</span>
                        <div class="stars">
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                        </div>
                    </div>
                    <div class="text">
                        <p>{{__('text.testimonials_t_1')}}</p>
                    </div>
                </div>
                <div class="item">
                    <div class="head">
                        <span class="name">{!!__('text.testimonials_author_t_2')!!}</span>
                        <div class="stars">
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                        </div>
                    </div>
                    <div class="text">
                        <p>{{__('text.testimonials_t_2')}}</p>
                    </div>
                </div>
                <div class="item">
                    <div class="head">
                        <span class="name">{!!__('text.testimonials_author_t_3')!!}</span>
                        <div class="stars">
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                        </div>
                    </div>
                    <div class="text">
                        <p>{{__('text.testimonials_t_3')}}</p>
                    </div>
                </div>
                <div class="item">
                    <div class="head">
                        <span class="name">{!!__('text.testimonials_author_t_4')!!}</span>
                        <div class="stars">
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                        </div>
                    </div>
                    <div class="text">
                        <p>{{__('text.testimonials_t_4')}}</p>
                    </div>
                </div>
                <div class="item">
                    <div class="head">
                        <span class="name">{!!__('text.testimonials_author_t_5')!!}</span>
                        <div class="stars">
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                        </div>
                    </div>
                    <div class="text">
                        <p>{{__('text.testimonials_t_5')}}</p>
                    </div>
                </div>
                <div class="item">
                    <div class="head">
                        <span class="name">{!!__('text.testimonials_author_t_6')!!}</span>
                        <div class="stars">
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                            <span class="active"></span>
                        </div>
                    </div>
                    <div class="text">
                        <p>{{__('text.testimonials_t_6')}}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="item-box">
        <div class="footer-info">
            <div class="info-column">
                <div class="item">
                    <ul class="footer-nav">
                        <li><a href="{{ route('home.index') }}">{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                        <li><a href="{{ route('home.about') }}">{{__('text.common_about_us_main_menu_item')}}</a></li>
                        <li><a href="{{ route('home.help') }}">{{__('text.common_help_main_menu_item')}}</a></li>
                        <li><a href="{{ route('home.testimonials') }}">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                    </ul>
                </div>
                <div class="item">
                    <ul class="footer-nav">
                        <li><a href="{{ route('home.delivery') }}">{{__('text.common_shipping_main_menu_item')}}</a></li>
                        <li><a href="{{ route('home.moneyback') }}">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                        <li><a href="{{ route('home.contact_us') }}">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                    </ul>
                </div>
                <div class="item">
                    <a href="{{ route('home.affiliate') }}" class="btn btn-primary">{{__('text.common_affiliate_main_menu_button')}}</a>
                </div>
            </div>
            <div class="copyright">
                <p>
                    {{__('text.license_text_license1_1')}} {{Request::getHost()}} {{__('text.license_text_license1_2')}}
                    {{__('text.license_text_license2_d5')}}
                </p>
            </div>
        </div>
    </div>
</footer>
<div class="mob-nav">
    <a href="#" class="item js-menu">
        <span class="ico">
          <img src="{{ asset("$design/images/icon/ico-menu.svg") }}" alt="">
        </span>
        <span class="name">{{__('text.common_categories_menu')}}</span>
    </a>
    <a href="{{ route('home.index') }}" class="item">
        <span class="ico">
          <img src="{{ asset("$design/images/icon/ico-home.svg") }}" alt="">
        </span>
        <span class="name">{{__('text.common_home_main_menu_item')}}</span>
    </a>
    <a href="{{ config('app.url') }}/login" class="item" target="_blank">
        <span class="ico">
          <img src="{{ asset("$design/images/icon/ico-profile.svg") }}" alt="">
        </span>
        <span class="name">{{__('text.common_profile')}}</span>
    </a>
    <a @if ($cart_count != 0)href="{{ route('cart.index') }}"@endif class="item cart">
        <span class="ico">
            <img src="{{ asset("$design/images/icon/ico-cart.svg") }}" alt="">
            <span class="number">{{$cart_count}}</span>
        </span>
        <span class="name">{{ $Currency::convert($cart_total) }}</span>
    </a>
</div>

<input hidden id="stattemp" value="{$data.web_statistic.params_string}">

<script src="{{ asset("$design/js/app.js") }}"></script>
<script src="{{ asset("$design/js/slick.js") }}"></script>
<script src="{{ asset("$design/js/main.js") }}"></script>
<script src="{{ asset("/js/all_js.js") }}"></script>

</body>

</html>