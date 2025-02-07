@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-wrapper container">
    <main class="main">
        @if (count($products) == 0)
            <h2 class="no_product_head" style="margin-bottom: 20px">{{ __("text.common_product_text") }} «{{ $search_text }}» {{ __("text.search_not_found") }}</h2>
            <div class="no_product_text" style="margin-bottom: 10px">{{ __("text.search_not_carry") }} «{{ $search_text }}» {{ __("text.search_this_time") }}</div>
            <div class="no_product_text" style="margin-bottom: 20px">{{ __("text.search_product_request") }}</div>
            <div class="button" id="go_to_contact_us" style="margin-bottom: 20px" onclick="location.href = '{{ route('home.contact_us') }}'">
                {{ __("text.common_contact_us_main_menu_item") }}
            </div>

            {{-- <h2>{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h1> --}}
            <h2>{{__('text.search_result_best_for_search')}}</h1>
            <div class="product-cards">
                <div class="promo-cards">
                    <article class="promo-card promo-card">
                        <div class="promo-card__title">
                            <div class="card__link">{{Str::ucfirst(__('text.common_banner1_text1'))}} {{Str::ucfirst(__('text.common_banner1_text2'))}}</div>
                        </div>
                        <div class="promo-card__text">{{__('text.common_banner1_text3')}} <br> {{__('text.common_banner1_text4')}}</div>
                    </article>
                    <article class="promo-card promo-card--sale">
                        <div class="promo-card__title">
                            <div class="card__link">{{__('text.common_banner2_text1')}} <br>{!!__('text.common_banner2_text2')!!}</div>
                        </div>
                        <div class="promo-card__text">{{__('text.common_banner2_text3')}} {{__('text.common_banner2_text4')}}</div>
                    </article>
                </div>
                @foreach ($bestsellers as $product)
                    <article class="card">
                        @if ($product['image'] != 'gift-card' && $product['discount'] != 0)
                            <span class="card__label">-{{ $product['discount'] }}%</span>
                        @endif
                        <div class="card__img">
                            @if ($product['image'] == 'gift-card')
                                <picture>
                                    <source type="image/webp" srcset="{{ asset($design . '/images/products/gift-160w.webp 1x, ' . $design . '/images/products/gift-321w.webp 2x') }}">
                                    <img src="{{ asset($design . '/images/products/gift-160w.png') }}" srcset="{{ asset($design . '/images/products/gift-160w.png 1x, ' . $design . '/images/products/gift-321w.png 2x') }}" width="161" height="160" alt="{{ $product['image'] }}">
                                </picture>
                            @else
                                <picture style="max-height: 175px; max-width: 175px;">
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}" style="max-height: 175px; max-width: 175px; width: auto; height: auto;">
                                </picture>
                            @endif
                        </div>
                        <div class="card__content">
                            <h2 class="card__title">
                                <a class="card__link" href="{{ route('home.product', $product['url']) }}">
                                    {{ $product['name'] }}
                                </a>
                            </h2>
                            <span class="card__ingredient">
                                @foreach ($product['aktiv'] as $aktiv)
                                    {{ $aktiv['name'] }}
                                @endforeach
                            </span>
                            <p class="card__desc">{{ $product['desc'] }}</p>
                            <div class="card__controls">
                                <button class="card__button button" onclick="location.href = '{{ route('home.product', $product['url']) }}'">
                                    <span class="icon">
                                        <img src="{{ asset($design . '/images/icons/cart.svg') }}" class="inline-svg">
                                    </span>
                                    <span class="button__text">{{__('text.product_add_to_cart_text')}}</span>
                                </button>
                                <span class="card__price">{{ $Currency::convert($product['price'], false, true) }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <h1 class="h1">{{__('text.common_best_selling_title')}}</h1>
            <div class="product-cards">
                <div class="promo-cards">
                    <article class="promo-card promo-card">
                        <div class="promo-card__title">
                            <div class="card__link">{{Str::ucfirst(__('text.common_banner1_text1'))}} {{Str::ucfirst(__('text.common_banner1_text2'))}}</div>
                        </div>
                        <div class="promo-card__text">{{__('text.common_banner1_text3')}} <br> {{__('text.common_banner1_text4')}}</div>
                    </article>
                    <article class="promo-card promo-card--sale">
                        <div class="promo-card__title">
                            <div class="card__link">{{__('text.common_banner2_text1')}} <br>{!!__('text.common_banner2_text2')!!}</div>
                        </div>
                        <div class="promo-card__text">{{__('text.common_banner2_text3')}} {{__('text.common_banner2_text4')}}</div>
                    </article>
                </div>
                @foreach ($products as $product)
                    <article class="card">
                        @if ($product['image'] != 'gift-card' && $product['discount'] != 0)
                            <span class="card__label">-{{ $product['discount'] }}%</span>
                        @endif
                        <div class="card__img">
                            @if ($product['image'] == 'gift-card')
                                <picture>
                                    <source type="image/webp" srcset="{{ asset($design . '/images/products/gift-160w.webp 1x, ' . $design . '/images/products/gift-321w.webp 2x') }}">
                                    <img src="{{ asset($design . '/images/products/gift-160w.png') }}" srcset="{{ asset($design . '/images/products/gift-160w.png 1x, ' . $design . '/images/products/gift-321w.png 2x') }}" width="161" height="160" alt="{{ $product['image'] }}">
                                </picture>
                            @else
                                <picture style="max-height: 175px; max-width: 175px;">
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}" style="max-height: 175px; max-width: 175px; width: auto; height: auto;">
                                </picture>
                            @endif
                        </div>
                        <div class="card__content">
                            <h2 class="card__title">
                                <a class="card__link" href="{{ route('home.product', $product['url']) }}">
                                    {{ $product['name'] }}
                                </a>
                            </h2>
                            <span class="card__ingredient">
                                @foreach ($product['aktiv'] as $aktiv)
                                    {{ $aktiv['name'] }}
                                @endforeach
                            </span>
                            <p class="card__desc">{{ $product['desc'] }}</p>
                            <div class="card__controls">
                                <button class="card__button button" onclick="location.href = '{{ route('home.product', $product['url']) }}'">
                                    <span class="icon">
                                        <img src="{{ asset($design . '/images/icons/cart.svg') }}" class="inline-svg">
                                    </span>
                                    <span class="button__text">{{__('text.product_add_to_cart_text')}}</span>
                                </button>
                                <span class="card__price">{{ $Currency::convert($product['price'], false, true) }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </main>
    <aside class="aside">
        <nav class="accordion aside-nav">
            <div class="accordion-item">
                <button class="accordion-button main_bestsellers" aria-expanded="true">
                    <span class="button-text">{{__('text.common_best_selling_title')}}</span>
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                        </svg>
                    </span>
                </button>
                <div class="accordion-panel">
                    <div class="accordion-content">
                        <ul class="aside-nav__list">
                            @foreach ($bestsellers as $bestseller)
                                <li class="aside-nav__item">
                                    <a class="aside-nav__link" href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}
                                        <span class="aside-nav__price">{{ $Currency::convert($bestseller['price'], false, true) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @foreach ($menu as $category)
                <div class="accordion-item">
                    <button class="accordion-button">
                        <span class="button-text">{{ $category['name'] }}</span>
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                            </svg>
                        </span>
                    </button>
                    <div class="accordion-panel">
                        <div class="accordion-content">
                            <ul class="aside-nav__list">
                                @foreach ($category['products'] as $item)
                                    <li class="aside-nav__item">
                                        <a class="aside-nav__link" href="{{ route('home.product', $item['url']) }}">{{ $item['name'] }}
                                            <span class="aside-nav__price">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </nav>
    </aside>
</div>
@endsection

@section('rewies')
<div class="container">
    <div class="footer-testimonials">
        <div class="testimonial">
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
        <div class="testimonial">
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
        <div class="testimonial">
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
        <div class="testimonial">
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
</div>
@endsection