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

            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                @php
                    $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
                @endphp
                <div class="button" id="go_to_contact_us" style="margin-bottom: 20px" onclick="location.href = '{{ route('home.contact_us', '_' . $domainWithoutZone) }}'">
                    {{ __("text.common_contact_us_main_menu_item") }}
                </div>
            @else
                <div class="button" id="go_to_contact_us" style="margin-bottom: 20px" onclick="location.href = '{{ route('home.contact_us', '') }}'">
                    {{ __("text.common_contact_us_main_menu_item") }}
                </div>
            @endif

            {{-- <h2>{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h1> --}}
            <h2 style="margin-bottom: 20px;">{{__('text.search_result_best_for_search')}}</h2>
            <div class="product-cards">
                <div class="cards">
                    @foreach ($bestsellers as $product)
                        <article class="card @if ($product['id'] == 616) card--gift @endif">
                            <div class="card__header">
                                <h2 class="card__title">
                                    <a href="{{ route('home.product', $product['url']) }}">{{ $product['name'] }}</a>
                                </h2>
                                @if (!in_array($product['id'], [616, 619, 620, 483, 484, 501, 615]))
                                    <div class="card__variants">
                                        @foreach ($product['product_dosages'] as $dosage)
                                            <span class="card__variant">{{ $dosage }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="card__img">
                                @if ($product['id'] == 616)
                                    <picture>
                                        <source type="image/webp" srcset="{{ asset("$design/img/products/gift-137w.webp") }} 1x, {{ asset("$design/img/products/gift-274w.webp") }} 2x"><img
                                            src="{{ asset("$design/img/products/gift-137w.png") }}"
                                            srcset="{{ asset("$design/img/products/gift-137w.png") }} 1x, {{ asset("$design/img/products/gift-274w.png") }} 2x" width="137" height="183"
                                            alt="{{ $product['image'] }}">
                                    </picture>
                                @else
                                    <picture style="max-height: 200px; max-width: 200px;">
                                        <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                        <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="max-height: 200px; max-width: 200px;">
                                    </picture>
                                @endif
                            </div>
                            <div class="card__footer">
                                <div class="card__price-wrapper">
                                    <span class="card__price">{{ $Currency::convert($product['price'], false, true) }}</span>
                                </div>
                                <button class="card__button button button--secondary" onclick="location.href = '{{ route('home.product', $product['url']) }}'">
                                    <span class="icon">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#cart") }}"></use>
                                        </svg>
                                    </span>
                                    {{ __('text.common_add_to_cart_text_d2') }}
                                </button>
                            </div>

                            {{-- @if ($product['id'] != 616)
                                <div class="card-features">
                                    @if ($product['discount'] != 0)
                                        <div class="card-feature card-feature--discount">-{{ $product['discount'] }}%</div>
                                    @endif
                                </div>
                            @endif --}}
                        </article>
                    @endforeach
                </div>
            </div>
        @else
            <h1 class="h1">{{__('text.search_result_title_page')}} «{{ $search_text }}»</h1>
            <div class="product-cards">
                <div class="cards">
                    @foreach ($products as $product)
                        <article class="card @if ($product['id'] == 616) card--gift @endif">
                            <div class="card__header">
                                <h2 class="card__title">
                                    <a href="{{ route('home.product', $product['url']) }}">{{ $product['name'] }}</a>
                                </h2>
                                @if (!in_array($product['id'], [616, 619, 620, 483, 484, 501, 615]))
                                    <div class="card__variants">
                                        @foreach ($product['product_dosages'] as $dosage)
                                            <span class="card__variant">{{ $dosage }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="card__img">
                                @if ($product['id'] == 616)
                                    <picture>
                                        <source type="image/webp" srcset="{{ asset("$design/img/products/gift-137w.webp") }} 1x, {{ asset("$design/img/products/gift-274w.webp") }} 2x"><img
                                            src="{{ asset("$design/img/products/gift-137w.png") }}"
                                            srcset="{{ asset("$design/img/products/gift-137w.png") }} 1x, {{ asset("$design/img/products/gift-274w.png") }} 2x" width="137" height="183"
                                            alt="{{ $product['image'] }}">
                                    </picture>
                                @else
                                    <picture style="max-height: 200px; max-width: 200px;">
                                        <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                        <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="max-height: 200px; max-width: 200px;">
                                    </picture>
                                @endif
                            </div>
                            <div class="card__footer">
                                <div class="card__price-wrapper">
                                    <span class="card__price">{{ $Currency::convert($product['price'], false, true) }}</span>
                                </div>
                                <button class="card__button button button--secondary" onclick="location.href = '{{ route('home.product', $product['url']) }}'">
                                    <span class="icon">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#cart") }}"></use>
                                        </svg>
                                    </span>
                                    {{ __('text.common_add_to_cart_text_d2') }}
                                </button>
                            </div>

                            {{-- @if ($product['id'] != 616)
                                <div class="card-features">
                                    @if ($product['discount'] != 0)
                                        <div class="card-feature card-feature--discount">-{{ $product['discount'] }}%</div>
                                    @endif
                                </div>
                            @endif --}}
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </main>
    <aside class="aside">
        <div class="accordion aside-nav" data-accordion>
            <details class="accordion-item @if($cur_category == '') is-open @endif" data-accordion-item @if($cur_category == '') open @endif>
                <summary class="accordion-button" data-accordion-button>
                    <span class="button-text">{{__('text.common_best_selling_title')}}</span>
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#paw") }}"></use>
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
                <details class="accordion-item @if(strtolower($cur_category) == strtolower($category['name'])) is-open @endif" data-accordion-item @if(strtolower($cur_category) == strtolower($category['name'])) open @endif>
                    <summary class="accordion-button" data-accordion-button>
                        <span class="button-text">{{ $category['name'] }}</span>
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#paw") }}"></use>
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
@endsection