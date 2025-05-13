@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="container page-wrapper">
<main class="main">
    @if (count($products) == 0)
        <h1 class="no_product_head" style="margin-bottom: 20px">{{ __("text.common_product_text") }} «{{ $search_text }}» {{ __("text.search_not_found") }}</h2>
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

        {{-- <h1>{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h1> --}}
        <h2>{{__('text.search_result_best_for_search')}}</h2>
        <div class="product-cards">
            @foreach ($bestsellers as $product)
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
                        <button class="button product-card__button" aria-label="{{__('text.common_buy_button')}}" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg#cart") }}"></use>
                                </svg>

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
    @else
        <h1>{{__('text.search_result_title_page')}} «{{ $search_text }}»</h1>
        <div class="product-cards">
            @foreach ($products as $product)
                <article class="card product-card">
                    @if ($product['id'] != 616 && $product['discount'] != 0)
                        <span class="card__label">-{{ $product['discount'] }}%</span>
                    @endif
                    <a class="product-card__img" href="{{ route('home.product', $product['url']) }}">
                        @if ($product['id'] == 616)
                            <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                        @else
                            <picture>
                                <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                            </picture>
                        @endif
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
                        <button class="button product-card__button" aria-label="{{__('text.common_buy_button')}}" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg#cart") }}"></use>
                                </svg>

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
    @endif
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
