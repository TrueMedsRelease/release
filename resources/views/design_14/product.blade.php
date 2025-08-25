@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<script>
    flagp = true;
</script>
<div class="cmcmodal hidden">
    <div class="bloktext">
       <p><b>{{random_int(2, 30)}}{{__('text.common_product1')}}</b>{{__('text.common_product2')}}</p>
    </div>
</div>
<div class="page-wrapper container">
    <main class="main">
        <article class="product-info">
            <div class="product-info__categories">
                @foreach ($product['categories'] as $category)
                    <a class="product-info__use" href="{{ route('home.category', $category['url']) }} ">{{ $category['name'] }}</a>
                @endforeach
            </div>
            <div class="product-info__card">
                <div class="product-info__img">
                    @if ($product['id'] == 616)
                        <picture>
                            <source type="image/webp"
                            srcset="{{ asset("$design/img/products/gift-product-175w.webp 1x, $design/img/products/gift-product-350w.webp 2x") }}"><img
                            src="{{ asset("$design/img/products/gift-product-175w.jpg") }}"
                            srcset="{{ asset("$design/img/products/gift-product-175w.jpg 1x, $design/img/products/gift-product-350w.jpg 2x") }}" width="175"
                            height="175" alt="Gift">
                        </picture>
                    @else
                        <picture style="max-height: 175px; max-width: 175px;">
                            <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                            <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="max-height: 175px; max-width: 175px;">
                        </picture>
                    @endif
                </div>
                <div class="product-info__content">
                    <div class="product-info__header">
                        <hgroup class="product-info__hgroup">
                            <h1 class="product-info__title">{{ $product['name'] }}</h1>
                            @if ($product['id'] != 616)
                                @if (count($product['aktiv']) > 0)
                                    <p class="product-info__ingredient">
                                        {!!__('text.product_active')!!}
                                        @foreach ($product['aktiv'] as $aktiv)
                                            <a href="{{ route('home.active', $aktiv['url']) }}">{{ $aktiv['name'] }}</a>
                                        @endforeach
                                    </p>
                                @endif
                            @endif
                        </hgroup>
                        <div class="card-features">
                            <div class="card-feature">
                                <picture>
                                    <source type="image/webp"
                                    srcset="{{ asset("$design/img/products/card-feature-1-48w.webp 1x, $design/img/products/card-feature-1-97w.webp 2x") }}"><img
                                    src="{{ asset("$design/img/products/card-feature-1-48w.png") }}"
                                    srcset="{{ asset("$design/img/products/card-feature-1-48w.png 1x, $design/img/products/card-feature-1-97w.png 2x") }}"
                                    width="49" height="33" alt="Product feature">
                                </picture>
                            </div>
                            <div class="card-feature">
                                <picture>
                                    <source type="image/webp"
                                    srcset="{{ asset("$design/img/products/card-feature-2-46w.webp 1x, $design/img/products/card-feature-2-92w.webp 2x") }}"><img
                                    src="{{ asset("$design/img/products/card-feature-2-46w.png") }}"
                                    srcset="{{ asset("$design/img/products/card-feature-2-46w.png 1x, $design/img/products/card-feature-2-92w.png 2x") }}"
                                    width="46" height="36" alt="Product feature">
                                </picture>
                            </div>
                        </div>
                    </div>

                    @if ($product['id'] != 616)
                        <div class="product-info__stock">
                            {!!__('text.product_pack1_1')!!}<span class="c-text-grey">{{__('text.product_pack2_1')}}{{ random_int(10, 40) }}{{__('text.product_pack3_1')}}</span>
                        </div>
                    @endif

                    <p class="product-info__description">{{ $product['desc'] }}</p>
                </div>
            </div>
            <hr class="hr">
            <div class="product-info__rows">
                @if (count($product['disease']) > 0)
                    <div class="info-info__row">
                        {{__('text.product_diseases')}}
                        @foreach ($product['disease'] as $disease)
                            <a href="{{ route('home.disease', $disease['url']) }}">
                                {{ ucfirst($disease['name']) }}
                            </a>
                        @endforeach
                    </div>
                @endif

                @if (!empty($product['analog']))
                    <div class="info-info__row">
                        {{ $product['name'] }} {!!__('text.product_analogs')!!}
                        @if (count(value: $product['analog']) > 10)
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
                    <div class="info-info__row">
                        {{ $product['name'] }} {!!__('text.product_others')!!}
                        @if (count($product['sinonim']) > 10)
                            <div class="text-box">
                                <span class="text">
                                    @foreach ($product['sinonim'] as $sinonim)
                                        @php
                                            $sinonim['url'] = str_replace('.', ',', $sinonim['url']);
                                        @endphp
                                        <a href = "{{ route('home.product', $sinonim['url']) }}">
                                            {{ $sinonim['name'] }}
                                        </a>
                                    @endforeach
                                </span>
                                <a href="#" class="more">view all</a>
                            </div>
                        @else
                            @foreach ($product['sinonim'] as $sinonim)
                                @php
                                    $sinonim['url'] = str_replace('.', ',', $sinonim['url']);
                                @endphp
                                <a href = "{{ route('home.product', $sinonim['url']) }}">
                                    {{ $sinonim['name'] }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                @endif
            </div>
        </article>
        @foreach ($product['packs'] as $key => $dosage)
            <div class="panel">
                <div class="panel__header">
                    @if ($product['id'] != 616)
                        @if (in_array($product['id'], [619, 620, 483, 484, 501, 615]))
                            <h2 class="h2">{{ $product['name'] }}</h2>
                        @else
                            <h2 class="h2">{{ "{$product['name']} $key" }}@if ($loop->iteration == 1 && $product['rec_name'] != 'none'), <span class="panel__title-more"> {{__('text.product_need_more')}} <a class="link" href="{{route('home.product', $product['rec_url'])}}">{{ $product['rec_name'] }}</a></span>@endif </h2>
                        @endif
                    @else
                        {{ $product['name'] }}
                    @endif
                </div>
                <table class="table product-table">
                    <thead>
                        <tr>
                            <th>{{__('text.product_package_title')}}</th>
                            @if ($product['id'] != 616)
                                <th>{{__('text.product_price_per_pill_title')}}</th>
                            @endif
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
                                    <div class="product__info @if ($loop->iteration == 1 && $product['id'] != 616) product__info--sale @endif" @if ($loop->iteration == 1 && $product['id'] != 616) style="height: auto;" @endif>
                                        <div class="product__quantity">{{ "{$item['num']} {$product['type']}" }}</div>
                                        @if ($product['id'] != 616)
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

                                @if ($product['id'] != 616)
                                    <td class="product__price-per-pill">{{ $Currency::convert(round($item['price'] / $item['num'], 2), false, true) }}</td>
                                @endif

                                <td>
                                    <div class="product__price-wrapper">
                                        <div class="product__discount">
                                            @if ($loop->remaining != 1 && $product['id'] != 616)
                                                <s>{{ $Currency::convert($dosage['max_pill_price'] * $item['num'], true) }}</s>
                                                <span>-{{ abs(ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100)) }}%</span>
                                            @endif
                                        </div>
                                        <div class="product__price">
                                            @if ($product['id'] != 616)
                                                @if ($loop->remaining != 1)
                                                    {{__('text.cart_only')}} {{ $Currency::convert($item['price'], true) }}
                                                @else
                                                    {{ $Currency::convert($item['price'], true) }}
                                                @endif
                                            @else
                                                {{ $Currency::convert($item['price'], true) }}
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="product__button-wrapper">
                                    <form action="{{ route('cart.add', $item['id']) }}" method="post">
                                        @csrf
                                        @if (!in_array(App::currentLocale(), ['de', 'it', 'gr', 'nl', 'hu', 'pt']))
                                            <button class="button button--secondary product__button">
                                                <span class="icon">
                                                    <svg width="1em" height="1em" fill="currentColor">
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#cart") }}"></use>
                                                    </svg>
                                                </span>
                                                <span class="button__text">{{ __('text.product_add_to_cart_text') }}</span>
                                            </button>
                                        @else
                                            <button class="button button--secondary product__button" style="padding: 0;">
                                                <span class="icon" style="display: flex; width: 4rem; justify-content: center;">
                                                    <svg width="1em" height="1em" fill="currentColor">
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#cart") }}"></use>
                                                    </svg>
                                                </span>
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        @if ($product['full_desc'])
            <div class="content product-content">
                {!! $product['full_desc'] !!}
            </div>
        @endif

        @if ($product['id'] == 616)
            <div class="content">
                <p style="margin: 0;">
                    <strong>{{__('text.gift_card_title')}}</strong>
                    <br>
                    <br>
                    <ol style="padding-left: 20px; line-height: 20px;">
                        <li style="margin-bottom: 15px;">{{__('text.gift_card_text1')}}</li>
                        <li>{{__('text.gift_card_text2')}}</li>
                    </ol>
                </p>
            </div>
        @endif

        <div class="related-products">
            <h2 class="related-products__title">{{ __('text.recc_text') }}</h2>
            <div class="cards">
                @foreach ($recommendation as $product_data)
                    @if ($loop->iteration == 7)
                        @break
                    @endif
                    <article class="card">
                        <div class="card__header">
                            <h2 class="card__title"><a href="{{ route('home.product', $product_data['url']) }}">{{ $product_data['name'] }}</a></h2>
                            <div class="card__ingredients">
                                <span class="card__ingredient">
                                    @foreach ($product_data['aktiv'] as $aktiv)
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </span>
                            </div>
                        </div>
                        <div class="card__img">
                            <picture style="max-height: 126px; max-width: 126px;">
                                <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                                <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}" style="max-height: 126px; max-width: 126px;">
                            </picture>
                        </div>
                        <div class="card__variants">
                            @foreach ($product_data['product_dosages'] as $dosage)
                                <span class="card__variant">{{ $dosage }}</span>
                            @endforeach
                        </div>
                        <div class="card__footer">
                            <div class="card__price-wrapper">
                                <span class="card__price">{{ $Currency::convert($product_data['price'], false, true) }} {{ strtolower(__("text.common_per_pill")) }}</span>
                            </div>
                            <button class="card__button button button--outlined" onclick="location.href = '{{ route('home.product', $product_data['url']) }}'">
                                {{ __('text.product_add_to_cart_text') }}
                            </button>
                        </div>
                        <div class="card-features">
                            @if ($product_data['discount'] != 0)
                                <div class="card-feature card-feature--discount">-{{ $product_data['discount'] }}%</div>
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