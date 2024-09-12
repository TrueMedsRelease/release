@extends($design . '.layouts.main')

@section('title', $product['name'])

@section('content')
<script>
    flagp = true;
</script>
<main class="product">
	<div class="product__container">
		<div class="product__body">
			<aside class="product__aside">
				<div class="product__descr">
					<div class="product__image">
						<div class="product__image-wrapper">
							<picture>
								@if ($product['image'] != 'gift-card')
                                    <source srcset="{{ asset('images/' . $product['image'] . '.webp') }}" type="image/webp">
                                @endif
                                <img class="product-about__img" src="{{ $product['image'] != 'gift-card' ? asset('images/' . $product['image'] . '.webp') : asset($design . '/images/products/gift-card.svg') }}"
                                    alt="{{ $product['image'] }}">
							</picture>
						</div>
                        @if ($product['image'] != 'gift-card')
                            @if (count($product['aktiv']) != 0)
                                <p class="product__image-active">{!!__('text.product_active')!!}
                                    @foreach ($product['aktiv'] as $aktiv)
                                        <a href="{{ route('home.active', $aktiv) }}">
                                            {{ $aktiv }}
                                        </a>
                                    @endforeach
                                </p>
                            @endif
                            <p class="product__image-stock">{!!__('text.product_pack1_1')!!}<b  style="color: #f2d43a;">{{__('text.product_pack2_1')}}{{ random_int(10, 40) }}{{__('text.product_pack3_1')}}</b></p>
                        @endif
						<p class="product__image-descr">{{ $product['desc'] }}</p>
						@if (count($product['disease']) > 0)
							<div class="product__image-block_links">
								<h2 class="product__image_label">{{__('text.product_diseases')}}</h2>
								<div class="product__image_links">
									@foreach ($product['disease'] as $disease)
										<a href="{{ route('home.disease', str_replace(' ', '-', $disease)) }}">
											{{ ucfirst($disease) }}
										</a>
                                    @endforeach
								</div>
								@if (count($product['disease']) > 10)<div class="more">view all</div>@endif
							</div>
                        @endif
						@if (!empty($product['analog']))
							<div class="product__image-block_links">
								<h2 class="product__image_label">{{ $product['name'] }} {!!__('text.product_analogs')!!}</h2>
								<div class="product__image_links">
									@foreach ($product['analog'] as $analog)
										<a href="{{ route('home.product', $analog['url']) }}">
											{{ $analog['name'] }}
										</a>
                                    @endforeach
								</div>
								@if (count($product['analog']) > 10)<div class="more">view all</div>@endif
							</div>
                        @endif
						@if (!empty($product['sinonim']))
							<div class="product__image-block_links">
								<h2 class="product__image_label">{{ $product['name'] }} {!!__('text.product_others')!!}</h2>
								<div class="product__image_links">
									@foreach ($product['sinonim'] as $sinonim)
                                        <a href = "{{ route('home.product', $sinonim['url']) }}">
                                            {{ $sinonim['name'] }}
                                        </a>
                                    @endforeach
								</div>
								@if (count($product['sinonim']) > 10)<div class="more">view all</div>@endif
							</div>
                        @endif
					</div>
				</div>

				<div class="product__offers" data-da=".product__items, 650, last">
					<div class="offers">
						<div class="offers__item">
							<picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/01.png") }}" alt=""></picture>
						</div>
						<div class="offers__item">
							<picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/02.png") }}" alt=""></picture>
						</div>
					</div>
				</div>
			</aside>

			<div class="product__content">
				<div class="product__top-line" data-da=".product__image, 650, first">
					<h1 class="product__title">{{ $product['name'] }}</h1>
					<span class="product__group">
						@foreach ($product['categories'] as $category)
							<a href="{{ $category['url'] }}">{{ $category['name'] }}</a>
                        @endforeach
					</span>
				</div>
                @foreach ($product['packs'] as $key => $dosage)
				    <div class="product__items">
                        @php
                            $prev_dosage = 0;
                        @endphp
					@foreach ($dosage as $item)
                        @if ($loop->last)
                            @continue
                        @endif
                        @if ($loop->iteration != 1 && $key != $prev_dosage)
                                </tbody>
                                </table>
                            </div>
                        @endif
						@if ($key != $prev_dosage)
							<div class="page-product__item item-product-info">
							<h3 class="item-product-info__name">
                                {{ "{$product['name']} $key" }}
                            </h3>
							<table class="item-product-info__table">
							<thead>
							<tr class="item-product-info__row item-product-info__row--top">
								<th class="item-product-info__package">{{__('text.product_package_title')}}</th>
								<th class="item-product-info__per-pill">{{__('text.product_price_per_pill_title')}}</th>
								<th class="item-product-info__price">{{__('text.product_price_title')}}</th>
								<th class="item-product-info__btn"></th>
							</tr>
							</thead>
                            @php
                                $prev_dosage = $key;
                            @endphp
							<tbody>
                        @endif
						<tr class="item-product-info__row">
							<th class="item-product-info__package">{{ "{$item['num']} {$product['type']}" }}
                                @if ($item['price'] >= 300)
                                    <span class="item-product-info__delivery">{{__('text.cart_free_express')}}</span>
                                @elseif($item['price'] < 300 && $item['price'] >= 200)
                                    <span class="item-product-info__delivery">{{__('text.cart_free_regular')}}</span>
                                @endif
							</th>
							<th class="item-product-info__per-pill">{{ $Currency::convert(round($item['price'] / $item['num'], 2), false, true) }}</th>
							<th class="item-product-info__price">
                                @if ($loop->remaining != 1 && $product['image'] != 'gift-card')
									<span class="item-product-info__old-price">
										<span>{{ $Currency::convert($dosage['max_pill_price'] * $item['num'], true) }}</span>
										<span>-{{ ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) }}%</span>
									</span>
									<span class="item-product-info__new-price">{{__('text.cart_only')}} <span>{{ $Currency::convert($item['price'], true) }}</span></span>
                                @endif
                                @if ($product['image'] == 'gift-card')
                                    <span class="item-product-info__new-price">{{ $Currency::convert($item['price'], true) }}</span>
                                @endif
							</th>
							<th class="item-product-info__btn">
                                <form action="{{ route('cart.add', $item['id']) }}" method="post">
                                    @csrf
                                    <button type="submit" class="item-product-info__add-to-cart">
                                        <svg width="24" height="24">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart-2") }}"></use>
                                        </svg>
                                        <span>{{__('text.common_add_to_cart_text_d2')}}</span>
                                    </button>
                                </form>
							</th>
						</tr>
							</div>
                    @endforeach
						   </tbody>
						</table>
					</div>
        </div>
                @endforeach
                @if ($product['full_desc'])
                    <div class="page-product__info info-product" style="font-weight: 300; line-height: 1.6923076923;">
                        {!! $product['full_desc'] !!}
                    </div>
                @endif
        </div>
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
