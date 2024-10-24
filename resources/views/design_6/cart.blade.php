@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<script>
    flagc = true;
</script>
<main class="page-cart">
	<div class="page-cart__container">
		<div class="page-cart__body">
			<div class="page-cart__content basket" id="shopping_cart">

			</div>
			<aside class="page-cart__sidebar">
				<div class="page-cart__preference preference-page-cart">
					<div class="preference-page-cart__item">
						<div class="preference-page-cart__top">
							<div class="preference-page-cart__icon">
								<img src="{{ asset("$design/images/icons/f-01.svg") }}" alt="">
							</div>
							<h2 class="preference-page-cart__label">{{__('text.cart_free_regular')}}</h2>
						</div>
						<p class="preference-page-cart__descr">{{__('text.cart_sum_regular')}}</p>
					</div>
					<div class="preference-page-cart__item">
						<div class="preference-page-cart__top">
							<div class="preference-page-cart__icon">
								<img src="{{ asset("$design/images/icons/f-02.svg") }}" alt="">
							</div>
							<h2 class="preference-page-cart__label">{{__('text.cart_free_express')}}</h2>
						</div>
						<p class="preference-page-cart__descr">{{__('text.cart_sum_express')}}</p>
					</div>
					<div class="preference-page-cart__item">
						<div class="preference-page-cart__top">
							<div class="preference-page-cart__icon">
								<img src="{{ asset("$design/images/icons/f-03.svg") }}" alt="">
							</div>
							<h2 class="preference-page-cart__label">{{__('text.cart_secret1')}}	{{__('text.cart_secret2')}}</h2>
						</div>
						<p class="preference-page-cart__descr">{{__('text.cart_description_secret')}}</p>
					</div>
					<div class="preference-page-cart__item">
						<div class="preference-page-cart__top">
							<div class="preference-page-cart__icon">
								<img src="{{ asset("$design/images/icons/f-04.svg") }}" alt="">
							</div>
							<h2 class="preference-page-cart__label">{{__('text.cart_moneyback1')}} {{__('text.cart_moneyback2')}}</h2>
						</div>
						<p class="preference-page-cart__descr">{{__('text.cart_description_moneyback')}}</p>
					</div>
				</div>
				<div class="page-cart__offers">
					<div class="page-cart__offer">
						<picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/01.png") }}" alt=""></picture>
					</div>
					<div class="page-cart__offer">
						<picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/02.png") }}" alt=""></picture>
					</div>
				</div>
			</aside>
		</div>
	</div>

	<section class="subscribe__container">
        <div class="subscribe_body">
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

    <section class="page__testimonials testimonials">
        <div class="testimonials__container">
            <h2 class="visually-hidden">Title for seo</h2>
            <div class="testimonials__body">
                <div class="testimonials__item">
                    <div class="testimonials__row">
                        <div class="testimonials__name">{!!__('text.testimonials_author_t_1')!!}</div>
                        <div class="testimonials__stars">
                            <svg width="90" height="14">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                            </svg>
                        </div>
                    </div>
                    <p class="testimonials__text">{{__('text.testimonials_t_1')}}</p>
                </div>

                <div class="testimonials__item">
                    <div class="testimonials__row">
                        <div class="testimonials__name">{!!__('text.testimonials_author_t_2')!!}</div>
                        <div class="testimonials__stars">
                            <svg width="90" height="14">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                            </svg>
                        </div>
                    </div>
                    <p class="testimonials__text">{{__('text.testimonials_t_2')}}</p>
                </div>

                <div class="testimonials__item">
                    <div class="testimonials__row">
                        <div class="testimonials__name">{!!__('text.testimonials_author_t_3')!!}</div>
                        <div class="testimonials__stars">
                            <svg width="90" height="14">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                            </svg>
                        </div>
                    </div>
                    <p class="testimonials__text">{{__('text.testimonials_t_13')}}</p>
                </div>

                <div class="testimonials__item">
                    <div class="testimonials__row">
                        <div class="testimonials__name">{!!__('text.testimonials_author_t_4')!!}</div>
                        <div class="testimonials__stars">
                            <svg width="90" height="14">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                            </svg>
                        </div>
                    </div>
                    <p class="testimonials__text">{{__('text.testimonials_t_4')}}</p>
                </div>
            </div>
            <a href="{{ route('home.testimonials') }}" class="testimonials__button button">
                <span>{{__('text.common_testimonials_main_menu_item')}}</span>
                <svg width="16" height="16">
                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-right") }}"></use>
                </svg>
            </a>
        </div>
    </section>
</main>

@endsection
