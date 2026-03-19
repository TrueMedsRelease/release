@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="container page-wrapper">
    <main class="main">
        <h1>{{__('text.disease_disease_result_title')}} «{{ Str::ucfirst(str_replace('-', ' ', $disease)) }}»</h1>
        <div class="product-cards">
            @foreach ($products as $product)
                <article class="card product-card">
                    @if ($product['id'] != 616 && $product['discount'] != 0)
                        <span class="card__label">-{{ $product['discount'] }}%</span>
                    @endif
                    <a class="product-card__img" href="{{ route('home.product', $product['url']) }}">
                        @if ($product['id'] == 616)
                            <img loading="lazy" src="{{ asset($design . '/images/gift-card.svg') }}" alt="{{ $product['image'] }}">
                        @else
                            <picture>
                                <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                            </picture>
                        @endif
                        {{-- <img loading="lazy" src="{{ $product['id'] != 616 ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/products/gift-card.svg') }}" width="140" height="140" alt="{{ $product['name'] }}"> --}}
                    </a>
                    <h2 class="product-card__heading">
                        <a class="product-card__brand link-primary" href="{{ route('home.product', $product['url']) }}">{{ $product['name'] }}</a>
                    </h2>
                    <div class="product-card__active">
                        @foreach ($product['aktiv'] as $aktiv)
                            <a class="product-card__ingredient" href="{{ route('home.active', $aktiv['url']) }}">{{ $aktiv['name'] }}</a>
                        @endforeach
                    </div>
                    <a class="product-card__text link-primary" href="{{ route('home.product', $product['url']) }}">
                        {{ str()->limit($product['desc'], 120, $end='...') }}
                    </a>
                    <div class="product-card__controls">
                        <button class="button product-card__button" aria-label="Buy Now" onclick="location.href='{{ route('home.product', $product['url']) }}'">
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
                            </span> <span class="button__text">{{__('text.common_buy_button')}}</span>
                        </button>
                        <div class="product-card__price">{{ $Currency::convert($product['price'], false, true) }}</div>
                    </div>
                </article>
                @if ($loop->index == 1)
                    <div class="combo-cards">
                        <article class="card combo-card">
                            <a class="link-primary combo-card__link-wrapper" href="">
                                <h2 class="combo-card__title">{{Str::ucfirst(__('text.common_banner1_text1'))}} {{Str::ucfirst(__('text.common_banner1_text2'))}}</h2>
                                <div class="combo-card__text">{{__('text.common_banner1_text3')}} <br>{{__('text.common_banner1_text4')}}</div>
                            </a>
                        </article>
                        <article class="card combo-card combo-card--sale">
                            <a class="link-primary combo-card__link-wrapper" href="">
                                <h2 class="combo-card__title">{{__('text.common_banner2_text1')}} <br>{!!__('text.common_banner2_text2')!!}</h2>
                                <div class="combo-card__text">{{__('text.common_banner2_text3')}} {{__('text.common_banner2_text4')}}</div>
                            </a>
                        </article>
                    </div>
                @endif
            @endforeach
        </div>
    </main>

    <aside class="aside categories-sidebar">
		<div class="categories-sidebar__inner">
			<div data-spollers class="categories-sidebar__spollers spollers">
				<div class="spollers__item">
					<button type="button" data-spoller class="spollers__title _spoller-active">{{__('text.common_best_selling_title')}}</button>
					<ul class="spollers__body main_bestsellers" id="main_bestsellers_body">
						@foreach ($bestsellers as $bestseller)
							<li class="spollers__item-list"><a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span style="font-size: 13px; color: var(--color-secondary);">{{ $Currency::convert($bestseller['price'], false, true) }}</span></li>
                        @endforeach
					</ul>
				</div>
                @foreach ($menu as $category)
                    <div class="spollers__item">
                        <button type="button" data-spoller class="spollers__title">{{ $category['name'] }}</button>
                        <ul class="spollers__body">
                            @foreach ($category['products'] as $item)
                                <li class="spollers__item-list">
                                    <a href="{{ route('home.product', $item['url']) }}">
                                        {{ $item['name'] }}
                                    </a>
                                    <span style="font-size: 13px; color: var(--color-secondary);">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
			</div>
		</div>
	</aside>
</div>
  @endsection

  @section('rewies')
    <div class="footer-testimonials">
        <div class="testimonial card">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_1')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_1')}}</div>
        </div>
        <div class="testimonial card">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_7')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_7')}}</div>
        </div>
        <div class="testimonial card">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_13')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_13')}}</div>
        </div>
        <div class="testimonial card">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_17')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_17')}}</div>
        </div>
    </div>
@endsection