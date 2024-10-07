<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Title')</title>
    <meta name="Description" content="@yield('description', 'Description')">
    <meta name="Keywords" content="@yield('keywords', 'Keywords')">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#739d3e"/>
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
    const design = 6;
</script>
<div class="wrapper">
	<header class="header">
		<div class="header__phones-top top-phones-header">
            <div class="header__container">
                <div class="top-phones-header__items">
                    <div class="top-phones-header__item request" style="pointer-events: none; font-weight: 600"><a class="request_call">{{ __('text.common_callback') }}</a><div class="request_text">{{__('text.common_call_us_top')}}</div></div>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_1')}}">{{__('text.phones_title_phone_1_code')}}{{__('text.phones_title_phone_1')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_2')}}">{{__('text.phones_title_phone_2_code')}}{{__('text.phones_title_phone_2')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_3')}}">{{__('text.phones_title_phone_3_code')}}{{__('text.phones_title_phone_3')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_4')}}">{{__('text.phones_title_phone_4_code')}}{{__('text.phones_title_phone_4')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_5')}}">{{__('text.phones_title_phone_5_code')}}{{__('text.phones_title_phone_5')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_6')}}">{{__('text.phones_title_phone_6_code')}}{{__('text.phones_title_phone_6')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_7')}}">{{__('text.phones_title_phone_7_code')}}{{__('text.phones_title_phone_7')}}</a>
                </div>
            </div>
        </div>
		<div class="container header__container header__container--second">
			<div class="header__bottom">
				<div class="header__categories categories" data-da=".fixed-bar, 560, first">
					<button class="button-categories icon-menu" type="button">
								<span class="button-categories__menu-wrapper">
									<span class="button-categories__menu">
										<span></span>
									</span>
								</span>
						<span class="button-categories__text">{{__('text.common_categories_menu')}}</span>
						<div class="button-categories__arrow">
							<svg width="20" height="20">
								<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-down") }}"></use>
							</svg>
						</div>
					</button>
					<div class="categories__tabs tabs">
						<nav class="tabs__navigation">
							<aside class="categories-sidebar">
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
                                            <div class="spollers__item">
                                                <button type="button" data-spoller class="spollers__title @if($cur_category == $category['name']) _spoller-active @endif">{{ $category['name'] }}</button>
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
                                        @endforeach
									</div>
								</div>
							</aside>
						</nav>
						<div class="tabs__close-icon">
							<svg width="24" height="24">
								<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
							</svg>
						</div>
					</div>
				</div>
				<div class="header__menu menu">
					<nav class="menu__body">
						<ul class="menu__list">
							<li class="menu__item"><a href="{{ route('home.index') }}" class="menu__link">{{__('text.common_best_sellers_main_menu_item')}}</a></li>
							<li class="menu__item"><a href="{{ route('home.about') }}" class="menu__link">{{__('text.common_about_us_main_menu_item')}}</a></li>
							<li class="menu__item"><a href="{{ route('home.help') }}" class="menu__link">{{__('text.common_help_main_menu_item')}}</a></li>
							<li class="menu__item" data-da=".menu__subslist, 900, first"><a href="{{ route('home.testimonials') }}" class="menu__link">{{__('text.common_testimonials_main_menu_item')}}</a></li>
							<li class="menu__item" data-da=".menu__subslist, 950, first"><a href="{{ route('home.delivery') }}" class="menu__link">{{__('text.common_shipping_main_menu_item')}}</a></li>
							<li class="menu__item" data-da=".menu__subslist, 1000, first"><a href="{{ route('home.moneyback') }}" class="menu__link">{{__('text.common_moneyback_main_menu_item')}}</a></li>
							<li class="menu__item" data-da=".menu__subslist, 1050, first"><a href="{{ route('home.contact_us') }}" class="menu__link">{{__('text.common_contact_us_main_menu_item')}}</a></li>
							<li class="menu__dotts">
								<span></span>
								<ul class="menu__subslist">

								</ul>
							</li>
						</ul>
					</nav>
				</div>
			</div>
			<div class="header__body">
				<div class="header__right">
					<div class="header__top">
						<div class="header__inner">
							<div class="header__top-row">
								<a href="{{ route('home.index') }}" class="header__logo logo">
									<img src="{{ asset("$design/images/logo.svg") }}" width="145" height="46" alt="">
								</a>
							</div>
							<div class="header__info-row">
								<div class="header__actions actions">
                                    @if (count($Language::GetAllLanuages()) > 1)
                                        <div class="actions__item">
                                            <div class="actions__icon">
                                                <svg width="24" height="20">
                                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-global") }}"></use>
                                                </svg>
                                            </div>
                                            <div class="actions__select">
                                                <select name="form[]" class="form" onchange="location.href=this.options[this.selectedIndex].value" data-scroll>
                                                    @foreach ($Language::GetAllLanuages() as $language)
                                                        <option value="/lang={{$language['code']}}" @if (App::currentLocale() == $language['code']) selected @endif>{{$language['name']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (count($Currency::GetAllCurrency()) > 1)
                                        <div class="actions__item">
                                            <div class="actions__icon">
                                                <svg width="24" height="24">
                                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-wallet") }}"></use>
                                                </svg>
                                            </div>
                                            <div class="actions__select">
                                                <select name="form[]" class="form" onchange="location.href=this.options[this.selectedIndex].value" data-scroll>
                                                    @foreach ($Currency::GetAllCurrency() as $item)
                                                        <option value="/curr={{ $item['code'] }}" @if (session('currency') == $item['code']) selected @endif> {{ Str::upper($item['code']) }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
									<div class="actions__item profile">
										<div class="actions__icon">
											<svg width="24" height="24">
												<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-profile") }}"></use>
											</svg>
										</div>
										<div class="actions__select">
											<a href="{{ route('home.login') }}" class="item" target="_blank">
												<span class="name">{{__('text.common_profile')}}</span>
											</a>
										</div>
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
								<button type="button" class="header__cart cart" data-da=".fixed-bar, 560, last" @if ($cart_count != 0)onclick="location.href='{{ route('cart.index') }}'"@endif>
									<span class="cart__icon">
										<svg width="30" height="30">
											<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
										</svg>
                                        @if ($cart_count != 0)
                                            <span class="cart__quantity">{{$cart_count}}</span>
                                        @endif
									</span>
									<span class="cart__total">{{ $Currency::convert($cart_total) }}</span>
								</button>
							</div>
						</div>
					</div>
					<div class="header__content">
						<div class="header__verified verified-header">
							<div class="verified-header__info">
								<div class="verified-header__icon">
									<svg width="40" height="40">
										<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-verified") }}"></use>
									</svg>
								</div>
								<div class="verified-header__descr">
									<h3 class="verified-header__title">{{__('text.common_verified_d4')}}</h3>
									<p class="verified-header__text">{{__('text.common_approved_d4')}}</p>
								</div>
							</div>
							<div class="verified-header__partners partners-verified">
								<div class="partners-verified__item">
									<img src="{{ asset("$design/images/partners/fda.svg") }}" width="45" height="28" >
								</div>
								<div class="partners-verified__item">
									<picture><source srcset="{{ asset("$design/images/partners/pgeu.webp") }}" type="image/webp"><img src="{{ asset("$design/images/partners/pgeu.png") }}" width="39" height="32" ></picture>
								</div>
								<div class="partners-verified__item">
									<picture><source srcset="{{ asset("$design/images/partners/cipa.webp") }}" type="image/webp"><img src="{{ asset("$design/images/partners/cipa.png") }}" width="50" height="30" ></picture>
								</div>
								<div class="partners-verified__item">
									<img src="{{ asset("$design/images/partners/mastercard.svg") }}" width="30" height="26" >
								</div>
								<div class="partners-verified__item">
									<img src="{{ asset("$design/images/partners/visa.svg") }}" width="47" height="20" >
								</div>
								<div class="partners-verified__item">
									<img src="{{ asset("$design/images/partners/mcafee.svg") }}" width="60" height="18" >
								</div>
							</div>
						</div>
						<div class="header__preference">
							<div class="header__item-preference preference-header-item">
								<div class="preference-header-item__top">
									<div class="preference-header-item__icon">
										<svg width="40" height="40">
											<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-preference-01") }}"></use>
										</svg>
									</div>
									<h3 class="preference-header-item__title">{{__('text.common_save')}}</h3>
								</div>
								<p class="preference-header-item__descr">{{__('text.common_discount')}}</p>
							</div>
							<div class="header__item-preference preference-header-item">
								<div class="preference-header-item__top">
									<div class="preference-header-item__icon">
										<svg width="40" height="40">
											<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-preference-02") }}"></use>
										</svg>
									</div>
									<h3 class="preference-header-item__title">{{__('text.common_eco_product')}}</h3>
								</div>
								<p class="preference-header-item__descr">{{__('text.common_natural_med')}}</p>
							</div>
							<div class="header__item-preference preference-header-item">
								<div class="preference-header-item__top">
									<div class="preference-header-item__icon">
										<svg width="40" height="40">
											<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-preference-03") }}"></use>
										</svg>
									</div>
									<h3 class="preference-header-item__title">{{__('text.common_delivery')}}</h3>
								</div>
								<p class="preference-header-item__descr">{{__('text.common_receive')}}</p>
							</div>
							<div class="header__item-preference preference-header-item">
								<div class="preference-header-item__top">
									<div class="preference-header-item__icon">
										<svg width="40" height="40">
											<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-preference-04") }}"></use>
										</svg>
									</div>
									<h3 class="preference-header-item__title">{{__('text.common_moneyback')}}</h3>
								</div>
								<p class="preference-header-item__descr">{{__('text.common_refund')}}</p>
							</div>
						</div>
						<picture><source srcset="{{ asset("$design/images/decor/petal-4.webp") }}" type="image/webp"><img class="header__decor-content" src="{{ asset("$design/images/decor/petal-4.png") }}" width="39" height="66" ></picture>
					</div>
				</div>
				<div class="header__banner banner-header" data-da=".header__top, 479.98, last">
					<div class="banner-header__icon">
						<svg width="112" height="20">
							<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
						</svg>
					</div>
					<h2 class="banner-header__title">1 000 000</h2>
					<p class="banner-header__descr">{{__('text.common_customers')}}</p>
					<div class="banner-header__decor">
						<picture><source srcset="{{ asset("$design/images/decor/petal-2.webp") }}" type="image/webp"><img class="banner-header__petal-02" src="{{ asset("$design/images/decor/petal-2.png") }}" width="43" height="70"></picture>
						<picture><source srcset="{{ asset("$design/images/decor/petal-3.webp") }}" type="image/webp"><img class="banner-header__petal-03" src="{{ asset("$design/images/decor/petal-3.png") }}" width="36" height="107" ></picture>
						<picture><source srcset="{{ asset("$design/images/decor/family.webp") }}" type="image/webp"><img class="banner-header__family" src="{{ asset("$design/images/decor/family.png") }}" width="247" height="166" ></picture>
					</div>
				</div>
			</div>
			<div class="header__search search-bar" data-dev>
				<form class="search search-bar__input search-form" action="{{ route('search.search_product') }}" method="POST">
                    @csrf
					<div class="search__icon">
						<svg class="search-close" width="20" height="20">
							<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
						</svg>
					</div>
					<div class="search__input">
						<input autocomplete="off" id="autocomplete" type="text" name="search_text" data-error="Error" placeholder="{{__('text.common_search_herbal')}}">
					</div>
					<div class="search__icon search_button">
						<svg width="20" height="20">
							<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-search") }}"></use>
						</svg>
					</div>
				</form>
				<div class="search-bar__nav" data-simplebar data-simplebar-auto-hide="false">
					<ul class="search-bar__letter-list">
						@foreach (range('A', 'Z') as $l)
							<li class="search-bar__item-list">
								{{-- {if $active} --}}
									<a href="{{ route('home.first_letter', $l) }}">{{ $l }}</a>
								{{-- {else}
									<span>{$letter}</span>
								{/if} --}}
							</li>
                        @endforeach
					</ul>
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
                                        value="+{{ $item['phonecode'] }}">
                                        +{{ $item['phonecode'] }}
                                    </option>
                                @endforeach
							</select>
						</div>
						<div class="enter-info__input enter-info__input--country">
							<input required autocomplete="off" type="number" id="phone" name="phone" placeholder="000 000 00 00" class="input" maxlength = "14" oninput="maxLengthCheck(this)">
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

    @yield('content')

    <footer class="footer">
        <div class="footer__container">
            <div class="footer__inner">
                <ul class="footer__row footer__row--large">
                    <li class="footer__item"><a href="tel:{{__('text.phones_title_phone_1')}}" class="footer__phone">{{__('text.phones_title_phone_1_code')}}{{__('text.phones_title_phone_1')}}</a></li>
                    <li class="footer__item"><a href="tel:{{__('text.phones_title_phone_2')}}" class="footer__phone">{{__('text.phones_title_phone_2_code')}}{{__('text.phones_title_phone_2')}}</a></li>
                    <li class="footer__item"><a href="tel:{{__('text.phones_title_phone_3')}}" class="footer__phone">{{__('text.phones_title_phone_3_code')}}{{__('text.phones_title_phone_3')}}</a></li>
                    <li class="footer__item"><a href="tel:{{__('text.phones_title_phone_4')}}" class="footer__phone">{{__('text.phones_title_phone_4_code')}}{{__('text.phones_title_phone_4')}}</a></li>
                    <li class="footer__item"><a href="tel:{{__('text.phones_title_phone_5')}}" class="footer__phone">{{__('text.phones_title_phone_5_code')}}{{__('text.phones_title_phone_5')}}</a></li>
                    <li class="footer__item"><a href="tel:{{__('text.phones_title_phone_6')}}" class="footer__phone">{{__('text.phones_title_phone_6_code')}}{{__('text.phones_title_phone_6')}}</a></li>
                    <li class="footer__item"><a href="tel:{{__('text.phones_title_phone_7')}}" class="footer__phone">{{__('text.phones_title_phone_7_code')}}{{__('text.phones_title_phone_7')}}</a></li>
                </ul>
                <ul class="footer__row">
                    <li class="footer__item"><a href="{{ route('home.index') }}" class="footer__link">{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                    <li class="footer__item"><a href="{{ route('home.about') }}" class="footer__link">{{__('text.common_about_us_main_menu_item')}}</a></li>
                    <li class="footer__item"><a href="{{ route('home.help') }}" class="footer__link">{{__('text.common_help_main_menu_item')}}</a></li>
                    <li class="footer__item"><a href="{{ route('home.testimonials') }}" class="footer__link">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                    <li class="footer__item"><a href="{{ route('home.delivery') }}" class="footer__link">{{__('text.common_shipping_main_menu_item')}}</a></li>
                    <li class="footer__item"><a href="{{ route('home.moneyback') }}" class="footer__link">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                    <li class="footer__item"><a href="{{ route('home.contact_us') }}" class="footer__link">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                    <li class="footer__item"><a href="{{ route('home.login') }}" class="footer__link" target="_blank">{{__('text.common_profile')}}</a></li>
                </ul>
                <a href="{{ route('home.affiliate') }}" class="footer__button button">{{__('text.common_affiliate_main_menu_button')}}</a>
                <p class="footer__copyright">
                    {{__('text.license_text_license1_1')}} {{str_replace(['http://', 'https://'], '', env('APP_URL'))}} {{__('text.license_text_license1_2')}}
                    {{__('text.license_text_license2_d6')}}
                </p>
                <picture><source srcset="{{ asset("$design/images/decor/petal-footer.webp") }}" type="image/webp"><img class="footer__petal" src="{{ asset("$design/images/decor/petal-footer.png") }}" width="44" height="71"></picture>
                <picture><source srcset="{{ asset("$design/images/decor/petal-footer-02.webp") }}" type="image/webp"><img class="footer__petal-02" src="{{ asset("$design/images/decor/petal-footer-02.png") }}" width="96" height="107"></picture>
            </div>
        </div>
        <div class="fixed-bar">
            <a href="{{ route('home.index') }}" class="fixed-bar__item">
                <div class="fixed-bar__icon fixed-bar__icon--home">
                    <svg width="24" height="24">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-home") }}"></use>
                    </svg>
                </div>
                <div class="fixed-bar__label">{{__('text.common_home_main_menu_item')}}</div>
            </a>
            <a href="{{ route('home.login') }}" target="_blank" class="fixed-bar__item">
                <div class="fixed-bar__item">
                    <div class="fixed-bar__icon">
                        <svg width="24" height="24">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-profile") }}"></use>
                        </svg>
                    </div>
                    <div class="fixed-bar__label">{{__('text.common_profile')}}</div>
                </div>
            </a>
        </div>
    </footer>
        {{-- <div class="announce">
            {if $data.is_product_page}
                <div class="announce__item announce__item--green">
                <div class="announce__icon">
                <svg width="24" height="24">
                    <use xlink:href="{$path.image}/icons/icons.svg#svg-checkmark"></use>
                </svg>
            </div>
            <div class="announce__text"><b>{$data.rand}{#product1#}</b>{#product2#}</div>
                </div>
            {/if}
            {if $data.is_cart_page}
                <div class="announce__item announce__item--yellow">
                <div class="announce__icon">
                <svg width="24" height="24">
                    <use xlink:href="{$path.image}/icons/icons.svg#svg-clock"></use>
                </svg>
            </div>
            <div class="announce__text">{#cart1#}<b>{$data.customer.country}{#cart2#}</b></div>
                </div>
            {/if}
        </div> --}}
        <input hidden id="stattemp" value="{$data.web_statistic.params_string}">
        <script src="{{ asset("$design/js/app.js") }}"></script>
        <script src="{{ asset("$design/js/main.js") }}"></script>
        <script src="{{ asset("/js/all_js.js") }}"></script>
        </div>
    </body>