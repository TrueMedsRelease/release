@extends($design . '.layouts.main')

@section('title', __('text.cart_cart_title'))

@section('content')
<script>
    flagc = true;
</script>
<section class="pay-index">
    <div class="pay-index__container">
        <ul class="pay-index__list">
            <li class="pay-index__item">
                <img src="/images/pay_icons/visa.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/mastercard.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/maestro.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/discover.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/amex.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/jsb.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/unionpay.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/dinners-club.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/apple-pay.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/google-pay.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/amazon-pay.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/stripe.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/paypal.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/sepa.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/cashapp.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/adyen.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/skrill.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/worldpay.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/payline.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/bitcoin.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/binance-coin.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/ethereum.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/litecoin.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/tron.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/usdt(erc20).svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/usdt(trc20).svg" alt="">
            </li>
        </ul>
    </div>
</section>

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
									<img src="{{ asset("$design/images/icons/package.svg") }}" alt="">
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
									<img src="{{ asset("$design/images/icons/delivery.svg") }}" alt="">
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
									<img src="{{ asset("$design/images/icons/money.svg") }}" alt="">
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

    <div class="page_testimonials">
        <div class="testimonials_block">
            <div class="testimonials_head">
                {{__('text.common_testimonials_main_menu_item')}}
            </div>
            <div class="testimonials">
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img src="{{ asset("$design/images/man1.png") }}">
                    </div>
                    <div class="testimonial_name">
                        {!!__('text.testimonials_author_t_18')!!}
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_1')}}
                    </div>
                    <div class="testimonial_stars">
                        <img src="{{ asset("$design/images/stars.svg") }}">
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img src="{{ asset("$design/images/man2.png") }}">
                    </div>
                    <div class="testimonial_name">
                        {!!__('text.testimonials_author_t_16')!!}
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_4')}}
                    </div>
                    <div class="testimonial_stars">
                        <img src="{{ asset("$design/images/stars.svg") }}">
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img src="{{ asset("$design/images/man3.png") }}">
                    </div>
                    <div class="testimonial_name">
                        {{__('text.common_term_name_3')}}
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_9')}}
                    </div>
                    <div class="testimonial_stars">
                        <img src="{{ asset("$design/images/stars.svg") }}">
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img src="{{ asset("$design/images/man4.png") }}">
                    </div>
                    <div class="testimonial_name">
                        {!!__('text.testimonials_author_t_12')!!}
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_5')}}
                    </div>
                    <div class="testimonial_stars">
                        <img src="{{ asset("$design/images/stars.svg") }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
