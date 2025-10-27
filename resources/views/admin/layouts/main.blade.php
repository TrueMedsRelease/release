<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
 	<title>@yield('title', 'Defult')</title>
 	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
 	<meta http-equiv="content-script-type" content="text/javascript">
	<meta http-equiv="content-style-type" content="text/css">

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

    <link rel="icon" href="{{ asset('/admin_style/images/favicon/favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('/admin_style/images/favicon/apple-touch-icon-180x180.png') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset_ver('/admin_style/css/style.css') }}" />
    <script src="{{ asset("vendor/jquery/jquery-1.3.2.min.js") }}"></script>
    <script src="{{ asset("vendor/jquery/tooltip/tooltip.js") }}"></script>
    <script src="{{ asset("vendor/jquery/confirm/confirm.js") }}"></script>
	<script src="{{ asset("vendor/jquery/dialog/dialog.js") }}"></script>
	<script src="{{ asset("vendor/jquery/jqtransformplugin/jquery.jqtransform.js") }}"></script>
 	<script language="javascript">
		// $(document).ready(function(){
		// 		$('form').jqTransform({imgPath:''});

		// 		$('.confirm').confirm({
		// 			msg:'<span class="notice">{{__('text.admin_common_confirm_text')}}</span>',
		// 					timeout:8000,
		// 			stopAfter:'ok',
		// 			buttons: {
		// 				ok:'{{__('text.admin_common_ok_text')}}',
		// 				cancel:'{{__('text.admin_common_cancel_text')}}',
		// 				separator:'  '
		// 			}
		// 			});
		// });
		function show_loading_message(element_id, message){
			document.getElementById(element_id).className = "loading";
			if(message != ""){
				document.getElementById(element_id).innerHTML = message;
			}
		}
	</script>
</head>
<body>
	<div class="wrapper">
@if ($logged_in)
	<header class="header">
		<div class="header__container" style="margin-top: 15px">
			<div class="header__row">
				<div class="header__wrapper">
					<div class="header__menu menu">
						<nav class="menu__body">
							<ul class="menu__list">
								<li class="menu__item">
								    <a href="{{ route('admin.main_properties') }}">{{__('text.admin_common_main_menu_1_element')}}</a>
								</li>
                                <li class="menu__item">
								    <a href="{{ route('admin.admin_seo') }}">{{__('text.admin_common_main_menu_14_element')}}</a>
								</li>
								<li class="menu__item">
								    <a href="{{ route('admin.products') }}">{{__('text.admin_common_main_menu_4_element')}}</a>
								</li>
								<li class="menu__item">
								    <a href="{{ route('admin.available_products') }}">{{__('text.admin_common_main_menu_5_element')}}</a>
								</li>
								<li class="menu__item">
								    <a href="{{ route('admin.available_packagings') }}">{{__('text.admin_common_main_menu_6_element')}}</a>
								</li>
								<li class="menu__item">
								    <a href="{{ route('admin.index') }}">{{__('text.admin_common_main_menu_7_element')}}</a>
								</li>
                                <li class="menu__item">
								    <a href="{{ route('admin.admin_checkout') }}">Checkout</a>
								</li>
								<li class="menu__item">
								    <a href="{{ route('admin.admin_languages') }}">{{__('text.admin_common_main_menu_9_element')}}</a>
								</li>
								<li class="menu__item">
								    <a href="{{ route('admin.admin_currencies') }}">{{__('text.admin_common_main_menu_10_element')}}</a>
								</li>
								{{-- <li class="menu__item">
                                    <a href="Update">{{ __('text.admin_renewal_shop') }}</a>
								</li> --}}
                                <li class="menu__item">
								    <a href="{{ route('admin.admin_logout') }}" class="header__sign-out" data-da=".header__row, 479.98, last">
                                        <svg width="20" height="20">
                                            <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-sign-out") }}"></use>
                                        </svg>
                                    </a>
								</li>
							</ul>
						</nav>
					</div>
				</div>

				{{-- <div class="header__actions">
					<div class="header__profile profile-header" data-da=".header__row, 479.98, 1">
						<div class="profile-header__icon">
							<svg width="20" height="20">
								<use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-user") }}"></use>
							</svg>
						</div>
						<div class="profile-header__info">
							<p class="profile-header__name profile-header__name--desktop">Admin</p>
							<p class="profile-header__name profile-header__name--mobile">profile</p>
						</div>
					</div>
					<a href="{{ route('admin.admin_logout') }}" class="header__sign-out" data-da=".header__row, 479.98, last">
						<svg width="20" height="20">
							<use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-sign-out") }}"></use>
						</svg>
					</a>
				</div> --}}
			</div>
		</div>
	</header>
@endif
	<main class="page">
		<div class="page__container">
			<section class="page__inner">
				<h1 class="main-title" style="@yield('style_title', '')">@yield('page_name', 'Defult')</h1>

                @yield('content')

            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="footer__container">
            <section class="page__contacts">
                <div class="contacts" data-contacts-body="">
                    <h3 class="contacts__caption-social">contacts</h3>
                    <ul class="contacts__social-list">
                        <li class="contacts__social-item">
                            <a class="item-social">
                                <div class="item-social__icon">
                                    <svg width="23" height="20">
                                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-tg") }}"></use>
                                    </svg>
                                </div>
                                <div class="item-social__info">
                                    <h3 class="item-social__label">Telegram</h3>
                                    <p class="item-social__text">@johnmeds</p>
                                </div>
                            </a>
                        </li>
                        <li class="contacts__social-item">
                            <a class="item-social">
                                <div class="item-social__icon">
                                    <svg width="23" height="20">
                                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-tg") }}"></use>
                                    </svg>
                                </div>
                                <div class="item-social__info">
                                    <h3 class="item-social__label">Telegram</h3>
                                    <p class="item-social__text">@andmeds</p>
                                </div>
                            </a>
                        </li>
                        <li class="contacts__social-item">
                            <a class="item-social">
                                <div class="item-social__icon">
                                    <svg width="19" height="28">
                                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-jabber") }}"></use>
                                    </svg>
                                </div>
                                <div class="item-social__info">
                                    <h3 class="item-social__label">Jabber</h3>
                                    <p class="item-social__text">john@tjabb.com</p>
                                </div>
                            </a>
                        </li>
                        <li class="contacts__social-item">
                            <a class="item-social">
                                <div class="item-social__icon">
                                    <svg width="23" height="24">
                                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-jabber") }}"></use>
                                    </svg>
                                </div>
                                <div class="item-social__info">
                                    <h3 class="item-social__label">Jabber</h3>
                                    <p class="item-social__text">andy@tjabb.com</p>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </footer>

    </div>

    <script>
        const routeCheckCode = "{{ route('home.check_code') }}";

        const routeAdminRequestLogin = "{{ route('admin.request_login') }}";
        const routeAdminAddToMain = "{{ route('admin.add_to_main') }}";
        const routeAdminDeleteFromMain = "{{ route('admin.delete_from_main') }}";
        const routeAdminProductUpInSort = "{{ route('admin.product_up_in_sort') }}";
        const routeAdminProductDownInSort = "{{ route('admin.product_down_in_sort') }}";
        const routeAdminSaveUserProperties = "{{ route('admin.save_user_properties') }}";
        const routeAdminSaveTemplate = "{{ route('admin.save_template') }}";
        const routeAdminLoadPageProperties = "{{ route('admin.load_page_properties') }}";
        const routeAdminSavePageProperties = "{{ route('admin.save_page_properties') }}";
        const routeAdminAddToShowed = "{{ route('admin.add_to_showed') }}";
        const routeAdminDeleteFromShowed = "{{ route('admin.delete_from_showed') }}";
        const routeAdminLoadPackagingInfo = "{{ route('admin.load_packaging_info') }}";
        const routeAdminAddPackToShowed = "{{ route('admin.add_pack_to_showed') }}";
        const routeAdminDeletePackFromShowed = "{{ route('admin.delete_pack_from_showed') }}";
        const routeAdminPackagingUpInSort = "{{ route('admin.packaging_up_in_sort') }}";
        const routeAdminPackagingDownInSort = "{{ route('admin.packaging_down_in_sort') }}";
        const routeAdminLoadProductInfo = "{{ route('admin.load_product_info') }}";
        const routeAdminSaveProductInfo = "{{ route('admin.save_product_info') }}";
        const routeAdminLoadProductUrl = "{{ route('admin.load_product_url') }}";
        const routeAdminSaveProductUrl = "{{ route('admin.save_product_url') }}";
        const routeAdminSaveLanguagesInfo = "{{ route('admin.save_languages_info') }}";
        const routeAdminSaveCurrenciesInfo = "{{ route('admin.save_currencies_info') }}";
        const routeAdminLoadPixel = "{{ route('admin.load_pixel') }}";
        const routeAdminSavePixel = "{{ route('admin.save_pixel') }}";
        const routeAdminGiftCardInfo = "{{ route('admin.gift_card_info') }}";
        const routeAdminSaveCheckoutInfo = "{{ route('admin.save_checkout_info') }}";
        const routeAdminSaveSubscribeInfo = "{{ route('admin.save_subscribe_info') }}";
    </script>

    <script src="{{ asset_ver("admin_style/js/style.js") }}"></script>
</body>
</html>