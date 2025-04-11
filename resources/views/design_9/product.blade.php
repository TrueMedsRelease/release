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
<div class="bonus_block all_padding">
    <div class="bonus1">
        <img loading="lazy" src="{{ asset("$design/images/bonus1_1.png") }}">
    </div>
    <div class="bonus2">
        <img loading="lazy" src="{{ asset("$design/images/bonus2_2.png") }}">
    </div>
</div>
<main class="product">
    <div class="product__container">
        <div class="product__body">
            <aside class="product__aside">
                <div class="product__descr">
                    <div class="product__image">
                        <div class="product__image-wrapper">
                            @if ($product['image'] == 'gift-card')
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                                </picture>
                            @endif
                            {{-- @if ($product['image'] != 'gift-card')
                                <picture>
                                    <source srcset="{{ asset('images/' . $product['image'] . '.webp') }}" type="image/webp">
                                    <img loading="lazy" src="{{ asset('images/' . $product['image'] . '.webp') }}" alt="{{ $product['image'] }}">
                                </picture>
                            @else
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @endif --}}
                        </div>
                    </div>
                    <div class="product__details details-product">
                        @if ($product['image'] != 'gift-card')
                            @if (count($product['aktiv']) > 0)
                                <p class="details-product__row">{!!__('text.product_active')!!}
                                    @foreach ($product['aktiv'] as $aktiv)
                                        <a href="{{ route('home.active', $aktiv['url']) }}">
                                            {{ $aktiv['name'] }}
                                        </a>
                                    @endforeach
                                </p>
                            @endif

                            <p class="details-product__row">
                                {!!__('text.product_pack1_1')!!}
                                <b style="color: #f2d43a;">{{__('text.product_pack2_1')}}{{ random_int(10, 40) }}{{__('text.product_pack3_1')}}</b>
                            </p>
                        @endif

                        <p class="details-product__descr">{{ $product['desc'] }}</p>

                        @if (count($product['disease']) > 0)
                            <div class="details-product__block-links">
                                <h2 class="details-product__label">{{__('text.product_diseases')}}</h2>
                                <div class="details-product__links">
                                    @foreach ($product['disease'] as $disease)
                                        <a href="{{ route('home.disease', $disease['url']) }}">
                                            {{ ucfirst($disease['name']) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (!empty($product['analog']))
                            @if (count($product['analog']) > 10)
                                <div class="details-product__block-links">
                                    <h2 class="details-product__label">{{ $product['name'] }} {!!__('text.product_analogs')!!}</h2>
                                    <input type="checkbox" class="read-more-checker" id="read-more-checker" />
                                    <div class="details-product__links limiter">
                                        @foreach ($product['analog'] as $analog)
                                            <a href="{{ route('home.product', $analog['url']) }}">
                                                {{ $analog['name'] }}
                                            </a>
                                        @endforeach
                                        <div class="bottom"></div>
                                    </div>
                                    <label for="read-more-checker" class="read-more-button"></label>
                                </div>
                            @else
                                <div class="details-product__block-links">
                                    <h2 class="details-product__label">{{ $product['name'] }} {!!__('text.product_analogs')!!}</h2>
                                    <div class="details-product__links">
                                        @foreach ($product['analog'] as $analog)
                                            <a href="{{ route('home.product', $analog['url']) }}">
                                                {{ $analog['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if ($product['image'] != 'gift-card')
                            @if (!empty($product['sinonim']))
                                @if (count($product['sinonim']) > 10)
                                    <div class="details-product__block-links">
                                        <h2 class="details-product__label">{{ $product['name'] }} {!!__('text.product_others')!!}</h2>
                                        <input type="checkbox" class="read-more-checker" id="read-more-checker" />
                                        <div class="details-product__links limiter">
                                            @foreach ($product['sinonim'] as $sinonim)
                                                @php
                                                    $sinonim['url'] = str_replace('.', ',', $sinonim['url']);
                                                @endphp
                                                <a href="{{ route('home.product', $sinonim['url']) }}">
                                                    {{ $sinonim['name'] }}
                                                </a>
                                            @endforeach
                                            <div class="bottom"></div>
                                        </div>
                                        <label for="read-more-checker" class="read-more-button"></label>
                                    </div>
                                @else
                                    <div class="details-product__block-links">
                                        <h2 class="details-product__label">{{ $product['name'] }} {!!__('text.product_others')!!}</h2>
                                        <div class="details-product__links">
                                            @foreach ($product['sinonim'] as $sinonim)
                                                @php
                                                    $sinonim['url'] = str_replace('.', ',', $sinonim['url']);
                                                @endphp
                                                <a href = "{{ route('home.product', $sinonim['url']) }}">
                                                    {{ $sinonim['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </aside>

            <div class="product__content">
                <div class="product__top-line" data-da=".product__image, 650, last">
                    <h1 class="product__title">
                        {{ $product['name'] }}
                    </h1>
                    <span class="product__group">
                        @foreach ($product['categories'] as $category)
                            <a href="{{ route('home.category', $category['url']) }}">{{ $category['name'] }}</a> <br>
                        @endforeach
                    </span>
                </div>
                <div class="product__items  item-product-info">
                @foreach ($product['packs'] as $key => $dosage)
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
                        @endif
                        @if ($key != $prev_dosage)
                            <div class="product__item">
                            <h3 class="item-product-info__name">
                                @if ($product['image'] != 'gift-card')
                                    @if (in_array($product['id'], [619, 620, 483, 484, 501, 615]))
                                        {{ $product['name'] }}
                                    @else
                                        {{ "{$product['name']} $key" }} @if ($loop->parent->iteration == 1 && $product['rec_name'] != 'none'), {{__('text.product_need_more')}} <span  class="details-product__links"><a class="head_link" href="{{route('home.product', $product['rec_url'])}}">{{ $product['rec_name'] }}</a></span>   @endif
                                    @endif
                                @else
                                    {{ $product['name'] }}
                                @endif
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
                        @endif
                        <tbody @if ($loop->iteration == 1 && $product['image'] != 'gift-card') class="item-product-info__row--discount" @endif>
                        <tr class="item-product-info__row">
                            <th class="item-product-info__package">
                                {{ "{$item['num']} {$product['type']}" }}
                                @if ($product['image'] != 'gift-card')
                                    @if ($item['price'] >= 300)
                                        <span class="item-product-info__delivery">{{__('text.cart_free_express')}}</span>
                                    @elseif($item['price'] < 300 && $item['price'] >= 200)
                                        <span class="item-product-info__delivery">{{__('text.cart_free_regular')}}</span>
                                    @endif
                                @endif
                            </th>
                            <th class="item-product-info__per-pill">{{ $Currency::convert(round($item['price'] / $item['num'], 2), false, true) }}</th>
                            <th class="item-product-info__price">
                                @if ($loop->remaining != 1 && $product['image'] != 'gift-card')
                                    <span class="item-product-info__old-price">
                                        <span>{{ $Currency::convert($dosage['max_pill_price'] * $item['num']) }}</span>
                                        <span>-{{ ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) }}%</span>
                                    </span>
                                @endif
                                <span class="item-product-info__new-price">
                                    @if ($product['image'] != 'gift-card')
                                        {!!__('text.product_only')!!}&nbsp;<span>{{ $Currency::convert($item['price']) }}</span>
                                    @else
                                        {{ $Currency::convert($item['price']) }}
                                    @endif
                                </span>
                            </th>
                            <th class="item-product-info__btn">
                                <form method="POST" action="{{ route('cart.add', $item['id']) }}">
                                    @csrf
                                    <button type="submit" class="item-product-info__add-to-cart">
                                        <svg width="24" height="24">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                        </svg>
                                        <span>{{__('text.product_add_to_cart_text_d2')}}</span>
                                    </button>
                                </form>
                            </th>
                        </tr>
                        </div>
                    @endforeach
                    </tbody>
                    </table>
                @endforeach
                </div>
    <div class="product__info info-product">
        @if ($product['full_desc'])
            {!! $product['full_desc'] !!}
        @endif
        @if ($product['image'] == 'gift-card')
            <p>
                <strong>{{__('text.gift_card_title')}}</strong>
                <br>
                <br>
                <ol style="padding-left: 20px; line-height: 20px;">
                    <li style="margin-bottom: 15px;">{{__('text.gift_card_text1')}}</li>
                    <li>{{__('text.gift_card_text2')}}</li>
                </ol>
            </p>
        @endif
    </div>
</main>

<div class="rec__container" style="padding: 0 15px; margin-bottom: 20px;">
    <h2 class="bestsellers__title title" style="font-size: 32px;">{{__('text.recc_text')}}</h2>
    <div class="bestsellers__body">
        <div class="product_list">
            @foreach ($recommendation as $product_data)
                @if ($loop->iteration == 7)
                    @break
                @endif
                <div class="product_info">
                    @if ($product_data['discount'] != 0)
                        <span class="card__label">-{{ $product_data['discount'] }}%</span>
                    @endif
                    <div class="product_info_top">
                        <a href="{{ route('home.product', $product_data['url']) }}">
                            <div class="product_img">
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                                    <img src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}">
                                </picture>
                            </div>
                        </a>
                        <a href="{{ route('home.product', $product_data['url']) }}" class="product_center">
                            <div class="product_main">
                                <div class="product_text">
                                    <span class="product_name">{{ $product_data['name'] }}</span>
                                    <span class="product_active">
                                        @foreach ($product_data['aktiv'] as $aktiv)
                                            {{ $aktiv['name'] }}
                                        @endforeach
                                    </span>
                                </div>
                                <div class="product_desc top">{{ $product_data['desc'] }}</div>
                            </div>
                        </a>
                        <div class="product_right_block">
                            <div class="product_price">{{ $Currency::convert($product_data['price'], false, true) }}</div>
                            <button type="button" class="product-card__button button button--accent" title="{{__('text.product_add_to_cart_text')}}" onclick="location.href='{{ route('home.product', $product_data['url']) }}'">
                                <svg width="24" height="24">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart-white") }}"></use>
                                </svg>
                                <span>{{__('text.common_buy_button')}}</span>
                            </button>
                        </div>
                    </div>
                    <div class="product_info_bottom">
                        <div class="product_desc bottom">{{ $product_data['desc'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@section('testimonial')
    <div class="reviews_block">
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_1')!!}</div>
                <div class="stars">
                    <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_1')}}</div>
        </div>
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_7')!!}</div>
                <div class="stars">
                    <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_7')}}</div>
        </div>
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_13')!!}</div>
                <div class="stars">
                    <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_13')}}</div>
        </div>
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_17')!!}</div>
                <div class="stars">
                    <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_17')}}</div>
        </div>
    </div>
@endsection

@endsection
