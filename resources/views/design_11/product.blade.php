@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)
@section('header_class', 'header--secondary')

@section('content')
<div class="page-wrapper container">
    <main class="main main--aside">
        <div class="main__content">
            <div class="main__heading">
                <h1 class="h1">{{ $product['name'] }}</h1>
                @foreach ($product['categories'] as $category)
                    <a class="hgroup__link link" href="{{ route('home.category', $category['url']) }} ">{{ $category['name'] }}</a>
                @endforeach
            </div>
            @foreach ($product['packs'] as $key => $dosage)
                <div class="panel">
                    <div class="panel__header">
                        @if ($product['image'] != 'gift-card')
                            @if (in_array($product['id'], [619, 620, 483, 484, 501, 615]))
                                <h2 class="h2">{{ $product['name'] }}</h2>
                            @else
                                <h2 class="h2">{{ "{$product['name']} $key" }}@if ($loop->iteration == 1 && $product['rec_name'] != 'none'), {{__('text.product_need_more')}} <a class="link" href="{{route('home.product', $product['rec_url'])}}">{{ $product['rec_name'] }}</a> @endif </h2>
                            @endif
                        @else
                            {{ $product['name'] }}
                        @endif
                    </div>
                    <table class="table product-table">
                        <thead>
                            <tr>
                                <th>{{__('text.product_package_title')}}</th>
                                <th>{{__('text.product_price_per_pill_title')}}</th>
                                <th>{{__('text.product_price_title')}}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dosage as $item)
                                @if ($loop->last)
                                    @continue
                                @endif
                                <tr class="product">
                                    <td class="product__info-wrapper">
                                        <div class="product__info @if ($loop->iteration == 1 && $product['image'] != 'gift-card') product__info--sale @endif" @if ($loop->iteration == 1 && $product['image'] != 'gift-card') style="height: auto;" @endif>
                                            <div class="product__quantity">{{ "{$item['num']} {$product['type']}" }}</div>
                                            @if ($product['image'] != 'gift-card')
                                                <div class="product__delivery">
                                                    @if ($item['price'] >= 300)
                                                        {{__('text.cart_free_express')}}
                                                    @elseif($item['price'] < 300 && $item['price'] >= 200)
                                                        {{__('text.cart_free_regular')}}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="product__price-per-pill">{{ $Currency::convert(round($item['price'] / $item['num'], 2), false, true) }}</td>
                                    <td>
                                        <div class="product__price-wrapper">
                                            <div class="product__discount">
                                                @if ($loop->remaining != 1 && $product['image'] != 'gift-card')
                                                    <s>{{ $Currency::convert($dosage['max_pill_price'] * $item['num'], true) }}</s>
                                                    <span>-{{ ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) }}%</span>
                                                @endif
                                            </div>
                                            <div class="product__price">
                                                @if ($product['image'] != 'gift-card')
                                                    {{__('text.cart_only')}} {{ $Currency::convert($item['price'], true) }}
                                                @else
                                                    {{ $Currency::convert($item['price'], true) }}
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="product__button-wrapper">
                                        <form action="{{ route('cart.add', $item['id']) }}" method="post">
                                            @csrf
                                            <button class="button product__button">
                                                <span class="icon">
                                                    <img src="{{ asset($design . '/images/icons/cart.svg') }}" class="inline-svg">
                                                </span>
                                                <span class="button__text">{{__('text.product_add_to_cart_text_d2')}}</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            @if ($product['full_desc'])
                <div class="content">
                    {!! $product['full_desc'] !!}
                </div>
            @endif
        </div>
        <aside class="main__aside main__aside--mobile-first">
            <div class="info-panel">
                <div class="info-panel__header">
                    <div class="info-panel__image">
                        @if ($product['image'] == 'gift-card')
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/products/gift-175w.webp 1x, ' . $design . '/images/products/gift-350w.webp 2x') }}">
                                <img loading="lazy" src="{{ asset($design . '/images/products/gift-175w.jpg') }}" srcset="{{ asset($design . '/images/products/gift-175w.jpg 1x, ' . $design . '/images/products/gift-350w.jpg 2x') }}" width="175" height="175" alt="{{ $product['image'] }}">
                            </picture>
                        @else
                            <picture>
                                <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                            </picture>
                        @endif
                    </div>
                    <div class="info-panel__heading">
                        <h1 class="h1">{{ $product['name'] }}</h1>
                        @foreach ($product['categories'] as $category)
                            <a class="hgroup__link link" href="{{ route('home.category', $category['url']) }} ">{{ $category['name'] }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="info-panel__content">
                    @if ($product['image'] != 'gift-card')
                        @if (count($product['aktiv']) > 0)
                            <div class="info-panel__row">
                                {!!__('text.product_active')!!}
                                @foreach ($product['aktiv'] as $aktiv)
                                    <a href="{{ route('home.active', $aktiv['url']) }}">{{ $aktiv['name'] }}</a>
                                @endforeach
                            </div>
                        @endif

                        <div class="info-panel__row">{!!__('text.product_pack1_1')!!}<span class="c-red">{{__('text.product_pack2_1')}}{{ random_int(10, 40) }}{{__('text.product_pack3_1')}}</span></div>

                    @endif


                    <div class="info-panel__row">
                        {{ $product['desc'] }}
                    </div>

                    @if (count($product['disease']) > 0)
                        <div class="info-panel__row">
                            {{__('text.product_diseases')}}
                            @foreach ($product['disease'] as $disease)
                                <a
                                    href="{{ route('home.disease', str_replace(' ', '-', $disease)) }}">{{ ucfirst($disease) }}</a>
                            @endforeach
                        </div>
                    @endif

                    @if (!empty($product['analog']))
                        <div class="info-panel__row">
                            {{ $product['name'] }} {!!__('text.product_analogs')!!}
                            @if (count($product['analog']) > 10)
                                <div class="text-box">
                                    <span class="text">
                                        @foreach ($product['analog'] as $analog)
                                            <a href="{{ route('home.product', $analog['url']) }}"
                                                class="analog">{{ $analog['name'] }}</a>
                                        @endforeach
                                    </span>
                                    <a href="#" class="more">view all</a>
                                </div>
                            @else
                                @foreach ($product['analog'] as $analog)
                                    <a href="{{ route('home.product', $analog['url']) }}"
                                        class="analog">{{ $analog['name'] }}</a>
                                @endforeach
                            @endif
                        </div>
                    @endif

                    @if (!empty($product['sinonim']))
                        <div class="info-panel__row">
                            {{ $product['name'] }} {!!__('text.product_others')!!}
                            @if (count($product['sinonim']) > 10)
                                <div class="text-box">
                                    <span class="text">
                                        @foreach ($product['sinonim'] as $sinonim)
                                           <a href = "{{ route('home.product', $sinonim['url']) }}">
                                                {{ $sinonim['name'] }}
                                            </a>
                                        @endforeach
                                    </span>
                                    <a href="#" class="more">view all</a>
                                </div>
                            @else
                                @foreach ($product['sinonim'] as $sinonim)
                                    <a href = "{{ route('home.product', $sinonim['url']) }}">
                                        {{ $sinonim['name'] }}
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </aside>
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
                    <button class="accordion-button" @if($cur_category == $category['name']) aria-expanded="true" @endif>
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