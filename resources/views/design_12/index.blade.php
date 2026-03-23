@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-wrapper container">
    <main class="main">
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
            {{-- <article class="card bonus">
                <div class="card__img">
                    <picture style="max-height: 175px; max-width: 175px;">
                        <img src="{{ asset($design . '/images/bonus_programm.png') }}" style="max-height: 175px; max-width: 175px; width: auto; height: auto;">
                    </picture>
                </div>
                <div class="card__content">
                    <h2 class="card__title">
                        <a class="card__link" href="/">
                            Bonus Card & Referral Program
                        </a>
                    </h2>
                    <span class="card__ingredient">
                        Save & Earn
                    </span>
                </div>
            </article> --}}
            @foreach ($bestsellers as $product)
                <article class="card">
                    @if ($product['id'] != 616 && $product['discount'] != 0)
                        <span class="card__label">-{{ $product['discount'] }}%</span>
                    @endif
                    <div class="card__img">
                        @if ($product['id'] == 616)
                            <picture style="max-height: 175px; max-width: 175px;">
                                <img src="{{ asset($design . '/images/products/gift.png') }}" style="max-height: 175px; max-width: 175px; width: auto; height: auto;" alt="{{ $product['image'] }}">
                            </picture>
                        @else
                            <picture style="max-height: 175px; max-width: 175px;">
                                <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="max-height: 175px; max-width: 175px; width: auto; height: auto;">
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
    </main>
    <aside class="aside">
        <nav class="accordion aside-nav">
            <div class="accordion-item">
                <button class="accordion-button main_bestsellers" aria-expanded="true">
                    <span class="button-text">{{__('text.common_best_selling_title')}}</span>
                    <span class="icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em"  fill="currentColor">
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-rr-angle-small-down') }}"></use>
                            </svg>
                        @else
                            <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"  fill="currentColor">
                                <path d="M12.4726 6.27337C12.4106 6.21088 12.3369 6.16129 12.2556 6.12744C12.1744 6.0936 12.0872 6.07617 11.9992 6.07617C11.9112 6.07617 11.8241 6.0936 11.7428 6.12744C11.6616 6.16129 11.5879 6.21088 11.5259 6.27337L8.47256 9.3267C8.41058 9.38918 8.33685 9.43878 8.25561 9.47262C8.17437 9.50647 8.08723 9.5239 7.99923 9.5239C7.91122 9.5239 7.82408 9.50647 7.74284 9.47262C7.6616 9.43878 7.58787 9.38918 7.52589 9.3267L4.47256 6.27337C4.41058 6.21088 4.33685 6.16129 4.25561 6.12744C4.17437 6.0936 4.08723 6.07617 3.99923 6.07617C3.91122 6.07617 3.82408 6.0936 3.74284 6.12744C3.6616 6.16129 3.58787 6.21088 3.52589 6.27337C3.40173 6.39828 3.33203 6.56725 3.33203 6.74337C3.33203 6.91949 3.40173 7.08846 3.52589 7.21337L6.58589 10.2734C6.96089 10.6479 7.46922 10.8583 7.99923 10.8583C8.52923 10.8583 9.03756 10.6479 9.41256 10.2734L12.4726 7.21337C12.5967 7.08846 12.6664 6.91949 12.6664 6.74337C12.6664 6.56725 12.5967 6.39828 12.4726 6.27337Z" fill="currentColor"/>
                            </svg>
                        @endif
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
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em"  fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-rr-angle-small-down') }}"></use>
                                </svg>
                            @else
                                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"  fill="currentColor">
                                    <path d="M12.4726 6.27337C12.4106 6.21088 12.3369 6.16129 12.2556 6.12744C12.1744 6.0936 12.0872 6.07617 11.9992 6.07617C11.9112 6.07617 11.8241 6.0936 11.7428 6.12744C11.6616 6.16129 11.5879 6.21088 11.5259 6.27337L8.47256 9.3267C8.41058 9.38918 8.33685 9.43878 8.25561 9.47262C8.17437 9.50647 8.08723 9.5239 7.99923 9.5239C7.91122 9.5239 7.82408 9.50647 7.74284 9.47262C7.6616 9.43878 7.58787 9.38918 7.52589 9.3267L4.47256 6.27337C4.41058 6.21088 4.33685 6.16129 4.25561 6.12744C4.17437 6.0936 4.08723 6.07617 3.99923 6.07617C3.91122 6.07617 3.82408 6.0936 3.74284 6.12744C3.6616 6.16129 3.58787 6.21088 3.52589 6.27337C3.40173 6.39828 3.33203 6.56725 3.33203 6.74337C3.33203 6.91949 3.40173 7.08846 3.52589 7.21337L6.58589 10.2734C6.96089 10.6479 7.46922 10.8583 7.99923 10.8583C8.52923 10.8583 9.03756 10.6479 9.41256 10.2734L12.4726 7.21337C12.5967 7.08846 12.6664 6.91949 12.6664 6.74337C12.6664 6.56725 12.5967 6.39828 12.4726 6.27337Z" fill="currentColor"/>
                                </svg>
                            @endif
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