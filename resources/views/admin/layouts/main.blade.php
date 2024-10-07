<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
 	<title>@yield('title', 'Defult')</title>
 	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
 	<meta http-equiv="content-script-type" content="text/javascript">
	<meta http-equiv="content-style-type" content="text/css">
    <link rel="icon" href="{{ asset('/admin/images/favicon/favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('/admin/images/favicon/apple-touch-icon-180x180.png') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/style.css') }}" />
    <script src="{{ asset("vendor/jquery/jquery-1.3.2.min.js") }}"></script>
    <script src="{{ asset("vendor/jquery/tooltip/tooltip.js") }}"></script>
    <script src="{{ asset("vendor/jquery/confirm/confirm.js") }}"></script>
	<script src="{{ asset("vendor/jquery/dialog/dialog.js") }}"></script>
	<script src="{{ asset("vendor/jquery/jqtransformplugin/jquery.jqtransform.js") }}"></script>
 	<script language="javascript">
		$(document).ready(function(){
				$('form').jqTransform({imgPath:''});

				$('.confirm').confirm({
					msg:'<span class="notice">{{__('text.admin_common_confirm_text')}}</span>',
							timeout:8000,
					stopAfter:'ok',
					buttons: {
						ok:'{{__('text.admin_common_ok_text')}}',
						cancel:'{{__('text.admin_common_cancel_text')}}',
						separator:'  '
					}
					});
		});
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
		<div class="header__container">
			<div class="header__row">
				<div class="header__wrapper">
					<div class="header__menu menu">
						<nav class="menu__body">
							<ul class="menu__list">
								<li class="menu__item">
								    <a href="{{ route('admin.main_properties') }}">{{__('text.admin_common_main_menu_1_element')}}</a>
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
								    <a href="{{ route('admin.admin_languages') }}">{{__('text.admin_common_main_menu_9_element')}}</a>
								</li>
								<li class="menu__item">
								    <a href="{{ route('admin.admin_currencies') }}">{{__('text.admin_common_main_menu_10_element')}}</a>
								</li>
								{{-- <li class="menu__item">
								    <a href="{$path.page}/updates">{{__('text.admin_common_main_menu_11_element')}}</a>
								</li> --}}
							</ul>
						</nav>
					</div>

				</div>

				<div class="header__actions">
					<div class="header__profile profile-header" data-da=".header__row, 479.98, 1">
						<div class="profile-header__icon">
							<svg width="20" height="20">
								<use xlink:href="/admin/images/icons/icons.svg#svg-user"></use>
							</svg>
						</div>
						<div class="profile-header__info">
							<p class="profile-header__name profile-header__name--desktop">Admin</p>
							<p class="profile-header__name profile-header__name--mobile">profile</p>
						</div>
					</div>
					<a href="{{ route('admin.admin_logout') }}" class="header__sign-out" data-da=".header__row, 479.98, last">
						<svg width="20" height="20">
							<use xlink:href="/admin/images/icons/icons.svg#svg-sign-out"></use>
						</svg>
						<span>Exit</span>
					</a>
				</div>
			</div>
		</div>
	</header>
@endif
	<main class="page">
		<div class="page__container">
			<section class="page__inner">
				<h1 class="main-title">@yield('page_name', 'Defult')</h1>

                @yield('content')

            </section>
            <section class="page__contacts">
                <div class="contacts" data-contacts-body="">
                    <h3 class="contacts__caption-social">contacts</h3>
                    <ul class="contacts__social-list">
                        <li class="contacts__social-item">
                            <a class="item-social">
                                <div class="item-social__icon">
                                    <svg width="23" height="20">
                                        <use xlink:href="/admin/images/icons/icons.svg#svg-tg"></use>
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
                                        <use xlink:href="/admin/images/icons/icons.svg#svg-tg"></use>
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
                                        <use xlink:href="/admin/images/icons/icons.svg#svg-jabber"></use>
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
                                        <use xlink:href="/admin/images/icons/icons.svg#svg-jabber"></use>
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
    </main>

    <footer class="footer">
        <div class="footer__container">

        </div>
    </footer>

    </div>
    <script src="{{ asset("admin/js/style.js") }}"></script>
</body>
</html>