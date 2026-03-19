@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-wrapper container">
    @foreach ($products as $category)
        <main class="main">
            <h1 class="h1">{{ $category['name'] }}</h1>
            <div class="product-cards">
                @foreach ($category['products'] as $product)
                    <article class="card">
                        @if ($product['id'] != 616 && $product['discount'] != 0)
                            <span class="card__label">-{{ $product['discount'] }}%</span>
                        @endif
                        <div class="card__img">
                            @if ($product['id'] == 616)
                                <picture>
                                    <source type="image/webp" srcset="{{ asset($design . '/images/products/gift-175w.webp 1x, ' . $design . '/images/products/gift-350w.webp 2x') }}">
                                    <img loading="lazy" src="{{ asset($design . '/images/products/gift-175w.jpg') }}" srcset="{{ asset($design . '/images/products/gift-175w.jpg 1x, ' . $design . '/images/products/gift-350w.jpg 2x') }}" width="175" height="175" alt="{{ $product['image'] }}">
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
                            <span class="card__price">{{ $Currency::convert($product['price'], false, true) }}</span>
                            <button class="card__button button" onclick="location.href = '{{ route('home.product', $product['url']) }}'">
                                <span class="icon">
                                    <img src="{{ asset($design . '/images/icons/cart.svg') }}" class="inline-svg">
                                </span>
                                @if (!in_array(App::currentLocale(), ['de', 'it', 'gr', 'nl', 'hu', 'pt']))
                                    <span class="button__text">{{__('text.product_add_to_cart_text')}}</span>
                                @endif
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </main>
    @endforeach
    <aside class="aside">
        <nav class="accordion aside-nav">
            <div class="accordion-item">
                <button class="accordion-button main_bestsellers" aria-expanded="true">
                    <span class="button-text">{{__('text.common_best_selling_title')}}</span>
                    <span class="icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="none" width="1em" height="1em" fill="currentColor">
                                <path d="M9.47256 3.1972C9.41058 3.13471 9.33685 3.08512 9.25561 3.05127C9.17437 3.01743 9.08723 3 8.99923 3C8.91122 3 8.82408 3.01743 8.74284 3.05127C8.6616 3.08512 8.58787 3.13471 8.52589 3.1972L5.47256 6.25053C5.41058 6.31301 5.33685 6.36261 5.25561 6.39645C5.17437 6.4303 5.08723 6.44772 4.99923 6.44772C4.91122 6.44772 4.82408 6.4303 4.74284 6.39645C4.6616 6.36261 4.58787 6.31301 4.52589 6.25053L1.47256 3.1972C1.41058 3.13471 1.33685 3.08512 1.25561 3.05127C1.17437 3.01743 1.08723 3 0.999226 3C0.911218 3 0.824081 3.01743 0.742842 3.05127C0.661602 3.08512 0.587868 3.13471 0.525893 3.1972C0.401726 3.32211 0.332031 3.49107 0.332031 3.6672C0.332031 3.84332 0.401726 4.01229 0.525893 4.1372L3.58589 7.19719C3.96089 7.57172 4.46922 7.7821 4.99923 7.7821C5.52923 7.7821 6.03756 7.57172 6.41256 7.19719L9.47256 4.1372C9.59673 4.01229 9.66642 3.84332 9.66642 3.6672C9.66642 3.49107 9.59673 3.32211 9.47256 3.1972Z" fill="currentColor"/>
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
                    <button class="accordion-button" @if($cur_category == $category['name']) aria-expanded="true" @endif>
                        <span class="button-text">{{ $category['name'] }}</span>
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="none" width="1em" height="1em" fill="currentColor">
                                    <path d="M9.47256 3.1972C9.41058 3.13471 9.33685 3.08512 9.25561 3.05127C9.17437 3.01743 9.08723 3 8.99923 3C8.91122 3 8.82408 3.01743 8.74284 3.05127C8.6616 3.08512 8.58787 3.13471 8.52589 3.1972L5.47256 6.25053C5.41058 6.31301 5.33685 6.36261 5.25561 6.39645C5.17437 6.4303 5.08723 6.44772 4.99923 6.44772C4.91122 6.44772 4.82408 6.4303 4.74284 6.39645C4.6616 6.36261 4.58787 6.31301 4.52589 6.25053L1.47256 3.1972C1.41058 3.13471 1.33685 3.08512 1.25561 3.05127C1.17437 3.01743 1.08723 3 0.999226 3C0.911218 3 0.824081 3.01743 0.742842 3.05127C0.661602 3.08512 0.587868 3.13471 0.525893 3.1972C0.401726 3.32211 0.332031 3.49107 0.332031 3.6672C0.332031 3.84332 0.401726 4.01229 0.525893 4.1372L3.58589 7.19719C3.96089 7.57172 4.46922 7.7821 4.99923 7.7821C5.52923 7.7821 6.03756 7.57172 6.41256 7.19719L9.47256 4.1372C9.59673 4.01229 9.66642 3.84332 9.66642 3.6672C9.66642 3.49107 9.59673 3.32211 9.47256 3.1972Z" fill="currentColor"/>
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


@section('promo_bonus')
  <div class="promo-items">
      <div class="promo-item">
          <div class="promo-item__info">
              <div class="promo-item__title">{{Str::ucfirst(__('text.common_banner1_text1'))}} {{Str::ucfirst(__('text.common_banner1_text2'))}}</div>
              <div class="promo-item__text">{{__('text.common_banner1_text3')}} {{__('text.common_banner1_text4')}}</div>
          </div>
          <div class="promo-item__img">
              <picture>
                  <source type="image/webp" srcset="{{ asset($design . '/images/layout/promo-1-172w.webp 1x, ' . $design . '/images/layout/promo-1-344w.webp 2x') }}">
                  <img src="{{ asset($design . '/images/layout/promo-1-172w.png') }}" srcset="{{ asset($design . '/images/layout/promo-1-172w.png 1x, ' . $design . '/images/layout/promo-1-344w.png 2x') }}" width="172" height="112" alt="Promo">
              </picture>
          </div>
      </div>
      <div class="promo-item">
          <div class="promo-item__info">
              <div class="promo-item__title promo-item__title--green">{{__('text.common_banner2_text1')}} {!!__('text.common_banner2_text2')!!}</div>
              <div class="promo-item__text">{{__('text.common_banner2_text3')}} {{__('text.common_banner2_text4')}}</div>
          </div>
          <div class="promo-item__img">
              <picture>
                  <source type="image/webp" srcset="{{ asset($design . '/images/layout/promo-2-172w.webp 1x, ' . $design . '/images/layout/promo-2-344w.webp 2x') }}">
                  <img src="{{ asset($design . '/images/layout/promo-2-172w.png') }}" srcset="{{ asset($design . '/images/layout/promo-2-172w.png 1x, ' . $design . '/images/layout/promo-2-344w.png 2x') }}" width="172" height="112" alt="Promo">
              </picture>
          </div>
      </div>
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