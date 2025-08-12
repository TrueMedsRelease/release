@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-wrapper container">
    <main class="main">
        <h1 class="h1">{{__('text.first_letter_first_letter_result_title')}} «{{ $letter }}»</h1>
        <div class="product-cards">
            <div class="cards">
                @foreach ($products as $product)
                    <article class="card">
                        <div class="card__header">
                            <h2 class="card__title">
                                <a href="{{ route('home.product', $product['url']) }}">{{ $product['name'] }}</a>
                            </h2>
                            <div class="card__ingredients">
                                <span class="card__ingredient">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </span>
                            </div>
                        </div>
                        <div class="card__img">
                            @if ($product['id'] == 616)
                                <picture>
                                    <source type="image/webp" srcset="{{ asset("$design/img/products/gift-125w.webp 1x, $design/img/products/gift-251w.webp 2x") }}">
                                    <img src="{{ asset("$design/img/products/gift-125w.jpg") }}" srcset="{{ asset("$design/img/products/gift-125w.jpg 1x, $design/img/products/gift-251w.jpg 2x") }}"
                                    width="126" height="126" alt="{{ $product['image'] }}">
                                </picture>
                            @else
                                <picture style="max-height: 126px; max-width: 126px;">
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="max-height: 126px; max-width: 126px;">
                                </picture>
                            @endif
                        </div>
                        @if ($product['id'] != 616)
                            @if (!in_array($product['id'], [619, 620, 483, 484, 501, 615]))
                                <div class="card__variants">
                                    @foreach ($product['product_dosages'] as $dosage)
                                        <span class="card__variant">{{ $dosage }}</span>
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        <div class="card__footer">
                            <div class="card__price-wrapper">
                                @if ($product['id'] != 616)
                                    <span class="card__price">{{ $Currency::convert($product['price'], false, true) }} {{ strtolower(__("text.common_per_pill")) }}</span>
                                    {{-- <span class="card__discount">Retail Price €4.77</span> --}}
                                @else
                                    <span class="card__price">{{ $Currency::convert($product['price'], false, true) }}</span>
                                @endif
                            </div>
                            <button class="card__button button button--outlined" onclick="location.href = '{{ route('home.product', $product['url']) }}'">
                                {{ __('text.product_add_to_cart_text') }}
                            </button>
                        </div>

                        @if ($product['id'] != 616)
                            <div class="card-features">
                                @if ($product['discount'] != 0)
                                    <div class="card-feature card-feature--discount">-{{ $product['discount'] }}%</div>
                                @endif
                                <div class="card-feature">
                                    <picture>
                                        <source type="image/webp" srcset="{{ asset("$design/img/brands/brand-1-48w.webp 1x, $design/img/brands/brand-1-97w.webp 2x") }}">
                                        <img src="{{ asset("$design/img/brands/brand-1-48w.png") }}"
                                        srcset="{{ asset("$design/img/brands/brand-1-48w.png 1x, $design/img/brands/brand-1-97w.png 2x") }}" width="49" height="32"
                                        alt="Product feature">
                                    </picture>
                                </div>
                                <div class="card-feature">
                                    <picture>
                                        <source type="image/webp" srcset="{{ asset("$design/img/brands/brand-2-46w.webp 1x, $design/img/brands/brand-2-93w.webp 2x") }}">
                                        <img src="{{ asset("$design/img/brands/brand-2-46w.png") }}"
                                        srcset="{{ asset("$design/img/brands/brand-2-46w.png 1x, $design/img/brands/brand-2-93w.png 2x") }}" width="47" height="36"
                                        alt="Product feature">
                                    </picture>
                                </div>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </main>
    <aside class="aside">
        <div class="accordion aside-nav" data-accordion>
            <details class="accordion-item @if($cur_category == '') is-open @endif" data-accordion-item @if($cur_category == '') open @endif>
                <summary class="accordion-button" data-accordion-button>
                    <span class="button-text">{{__('text.common_best_selling_title')}}</span>
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                        </svg>
                    </span>
                </summary>
                <div class="accordion-panel" data-accordion-panel>
                    <div class="accordion-content content">
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
            </details>
            @foreach ($menu as $category)
                <details class="accordion-item @if($cur_category == $category['name']) is-open @endif" data-accordion-item @if($cur_category == $category['name']) open @endif>
                    <summary class="accordion-button" data-accordion-button>
                        <span class="button-text">{{ $category['name'] }}</span>
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                            </svg>
                        </span>
                    </summary>
                    <div class="accordion-panel" data-accordion-panel>
                        <div class="accordion-content content">
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
                </details>
            @endforeach
        </div>
    </aside>
</div>
@endsection

@section('rewies')
<div class="footer-testimonials">
    <div class="container">
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