@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<script>
    flagc = true;
</script>
<section class="pay-index cart-pay">
    <div class="pay-index__container">
        <ul class="pay-index__list">
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#visa">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#mastercard">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#maestro">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#discover">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#amex">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#jsb">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' union-pay' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#unionpay">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#dinners-club">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#apple-pay">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#google-pay">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#amazon-pay">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#stripe">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#paypal">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#sepa">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#cashapp">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#adyen">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#skrill">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#worldpay">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#payline">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#bitcoin">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#binance-coin">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#ethereum">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#litecoin">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#tron">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#usdt(erc20)">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#usdt(trc20)">
                </svg>
            </li>
        </ul>
    </div>
</section>

<main class="christmas main" style="display: none" onclick="location.href='{{ route('home.checkup') }}'">
    <img loading="lazy" src="{{ asset("/pub_images/checkup_img/white/checkup_big.png") }}">
</main>

<main class="page-cart">
	<div class="page-cart__container">
		<div class="page-cart__body">
			<div class="page-cart__content basket" id="shopping_cart">

			</div>
			<aside class="page-cart__sidebar">
				<div class="page-cart__preference preference-page-cart">
					<div class="preference_top_block">
						<div class="preference-page-cart__item">
							<div class="preference-page-cart__top">
								<div class="preference-page-cart__icon">
									<img loading="lazy" src="{{ asset("$design/images/icons/package.svg") }}" alt="">
								</div>
							</div>
							<div class="preference_text">
								<h2 class="preference-page-cart__label">{{__('text.cart_free_regular')}}</h2>
								<p class="preference-page-cart__descr">{{__('text.cart_sum_regular')}}</p>
							</div>
						</div>
						<div class="preference-page-cart__item">
							<div class="preference-page-cart__top">
								<div class="preference-page-cart__icon">
									<img loading="lazy" src="{{ asset("$design/images/icons/delivery.svg") }}" alt="">
								</div>
							</div>
							<div class="preference_text">
								<h2 class="preference-page-cart__label">{{__('text.cart_free_express')}}</h2>
								<p class="preference-page-cart__descr">{{__('text.cart_sum_express')}}</p>
							</div>
						</div>
					</div>
					<div class="preference_bottom_block">
						<div class="preference-page-cart__item">
							<div class="preference-page-cart__top">
								<div class="preference-page-cart__icon">
									<img loading="lazy" src="{{ asset("$design/images/icons/money.svg") }}" alt="">
								</div>
							</div>
							<div class="preference_text">
								<h2 class="preference-page-cart__label">{{__('text.cart_moneyback1')}} {{__('text.cart_moneyback2')}}</h2>
								<p class="preference-page-cart__descr">{{__('text.cart_description_moneyback')}}</p>
							</div>
						</div>
					</div>
				</div>
			</aside>
		</div>
	</div>

	<section class="subscribe_container">
        <div class="block_subscribe">
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
                <input type="text" placeholder="Email" class="form__input input" id="email_sub">
                <div class="button_sub">
                    <img loading="lazy" src="{{ asset("$design/images/icons/subscribe_mini.svg") }}" class="sub_mini">
                    <span class="button_text">{{__('text.common_subscribe')}}</span>
                </div>
            </div>
        </div>
    </section>

   <section class="ship-index">
        <div class="ship-index__container">
            <ul class="ship-index__list">
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usps' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#usps" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ems' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#ems" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dhl' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#dhl" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ups' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#ups" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fedex' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#fedex" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tnt' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#tnt" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' postnl' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#postnl" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' deutsche_post' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#deutsche_post" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dpd' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#dpd" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' gls' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#gls" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' australia_post' }}" @endif>
                        <use width="100%" height="100%" width="100%" href="/pub_images/shipping/sprite.svg#australia_post" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' colissimo' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#colissimo" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' correos' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#correos" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
            </ul>
        </div>
    </section>

    <div class="page_testimonials">
        <div class="testimonials_block">
            <div class="testimonials_head">
                {{__('text.common_testimonials_main_menu_item')}}
            </div>
            <div class="testimonials">
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img loading="lazy" src="{{ asset("$design/images/man1.png") }}">
                    </div>
                    <div class="testimonial_name">
                        {!!__('text.testimonials_author_t_18')!!}
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_1')}}
                    </div>
                    <div class="testimonial_stars">
                        <img loading="lazy" src="{{ asset("$design/images/stars.svg") }}">
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img loading="lazy" src="{{ asset("$design/images/man2.png") }}">
                    </div>
                    <div class="testimonial_name">
                        {!!__('text.testimonials_author_t_16')!!}
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_4')}}
                    </div>
                    <div class="testimonial_stars">
                        <img loading="lazy" src="{{ asset("$design/images/stars.svg") }}">
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img loading="lazy" src="{{ asset("$design/images/man3.png") }}">
                    </div>
                    <div class="testimonial_name">
                        {{__('text.common_term_name_3')}}
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_9')}}
                    </div>
                    <div class="testimonial_stars">
                        <img loading="lazy" src="{{ asset("$design/images/stars.svg") }}">
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img loading="lazy" src="{{ asset("$design/images/man4.png") }}">
                    </div>
                    <div class="testimonial_name">
                        {!!__('text.testimonials_author_t_12')!!}
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_5')}}
                    </div>
                    <div class="testimonial_stars">
                        <img loading="lazy" src="{{ asset("$design/images/stars.svg") }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
