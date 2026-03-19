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
{{-- <div class="bonus_block all_padding big">
    <div class="bonus1">
        <img loading="lazy" src="{{ asset("$design/images/bonus_programm.png") }}">
    </div>
    <div class="bonus1">
        <img loading="lazy" src="{{ asset("$design/images/happy_day.png") }}">
    </div>
    <div class="bonus2">
        <img loading="lazy" src="{{ asset("$design/images/super_sale.png") }}">
    </div>
</div>
<div class="bonus_block all_padding small">
    <div class="bonus1">
        <img loading="lazy" src="{{ asset("$design/images/bonus_1_small.png") }}">
    </div>
    <div class="bonus1">
        <img loading="lazy" src="{{ asset("$design/images/bonus_2_small.png") }}">
    </div>
    <div class="bonus2">
        <img loading="lazy" src="{{ asset("$design/images/bonus_3_small.png") }}">
    </div>
</div> --}}
<main class="product">
    <div class="product__container">
        <div class="product__body">
            <aside class="product__aside">
                <div class="product__descr">
                    <div class="product__image">
                        <div class="product__image-wrapper">
                            @if ($product['id'] == 616)
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                                </picture>
                            @endif
                            {{-- @if ($product['id'] != 616)
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
                        @if ($product['id'] != 616)
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

                        @if ($product['id'] != 616)
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
                                @if ($product['id'] != 616)
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
                                @if ($product['id'] != 616)
                                    <th class="item-product-info__per-pill">{{__('text.product_price_per_pill_title')}}</th>
                                @endif
                                <th class="item-product-info__price">{{__('text.product_price_title')}}</th>
                                <th class="item-product-info__btn"></th>
                            </tr>
                            </thead>
                            @php
                                $prev_dosage = $key;
                            @endphp
                        @endif
                        <tbody @if ($loop->iteration == 1 && $product['id'] != 616) class="item-product-info__row--discount" @endif>
                        <tr class="item-product-info__row">
                            <th class="item-product-info__package">
                                {{ "{$item['num']} {$product['product_types'][$item['type_id']]}" }}
                                @if ($product['id'] != 616)
                                    @if ($item['price'] >= 300)
                                        <span class="item-product-info__delivery">{{__('text.cart_free_express')}}</span>
                                    @elseif($item['price'] < 300 && $item['price'] >= 200)
                                        <span class="item-product-info__delivery">{{__('text.cart_free_regular')}}</span>
                                    @endif
                                @endif
                            </th>

                            @if ($product['id'] != 616)
                                <th class="item-product-info__per-pill">{{ $Currency::convert(round($item['price'] / $item['num'], 2), false, true) }}</th>
                            @endif

                            <th class="item-product-info__price">
                                @if ($loop->remaining != 1 && $product['id'] != 616)
                                    <span class="item-product-info__old-price">
                                        <span>{{ $Currency::convert($dosage['max_pill_price'] * $item['num']) }}</span>
                                        <span>-{{ abs(ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100)) }}%</span>
                                    </span>
                                @endif
                                <span class="item-product-info__new-price">
                                    @if ($product['id'] != 616)
                                        @if ($loop->remaining != 1)
                                            {{__('text.cart_only')}} {{ $Currency::convert($item['price'], true) }}
                                        @else
                                            {{ $Currency::convert($item['price'], true) }}
                                        @endif
                                    @else
                                        {{ $Currency::convert($item['price'], true) }}
                                    @endif
                                </span>
                            </th>
                            <th class="item-product-info__btn">
                                <form method="POST" action="{{ route('cart.add', $item['id']) }}">
                                    @csrf
                                    <button type="submit" class="item-product-info__add-to-cart">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="24" height="24">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.4697 0.46967C13.7626 0.176777 14.2375 0.176777 14.5304 0.46967L18.3107 4.25001H21C21.4142 4.25001 21.75 4.5858 21.75 5.00001C21.75 5.41422 21.4142 5.75001 21 5.75001H20.4557C20.2293 6.60606 19.9365 7.59301 19.5959 8.74137L18.5708 12.1969C18.1636 13.5702 17.914 14.412 17.4765 15.0966C16.7233 16.2751 15.5663 17.1386 14.2223 17.5256C13.4416 17.7503 12.5635 17.7502 11.1312 17.75H10.8688C9.43649 17.7502 8.55843 17.7503 7.77772 17.5256C6.43365 17.1386 5.2767 16.2751 4.52349 15.0966C4.08598 14.412 3.83637 13.5702 3.42921 12.1969L2.40431 8.74199C2.06357 7.59338 1.77073 6.60621 1.54431 5.75001H1C0.585786 5.75001 0.25 5.41422 0.25 5.00001C0.25 4.5858 0.585786 4.25001 1 4.25001H3.93198L7.71231 0.469685C8.0052 0.176792 8.48008 0.176792 8.77297 0.469685C9.06586 0.762578 9.06586 1.23745 8.77297 1.53035L6.05331 4.25001H16.1894L13.4697 1.53033C13.1768 1.23744 13.1768 0.762563 13.4697 0.46967ZM9.75 9.00002C9.75 8.5858 9.41421 8.25002 9 8.25002C8.58579 8.25002 8.25 8.5858 8.25 9.00002V13C8.25 13.4142 8.58579 13.75 9 13.75C9.41421 13.75 9.75 13.4142 9.75 13V9.00002ZM13.75 9.00002C13.75 8.5858 13.4142 8.25002 13 8.25002C12.5858 8.25002 12.25 8.5858 12.25 9.00002V13C12.25 13.4142 12.5858 13.75 13 13.75C13.4142 13.75 13.75 13.4142 13.75 13V9.00002Z"/>
                                            </svg>
                                        @endif
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
        @if ($product['id'] == 616)
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
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="24" height="24">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart-white") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="24" height="24">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.4697 3.46967C14.7626 3.17678 15.2375 3.17678 15.5304 3.46967L19.3107 7.25001H22C22.4142 7.25001 22.75 7.5858 22.75 8.00001C22.75 8.41422 22.4142 8.75001 22 8.75001H21.4557C21.2293 9.60606 20.9365 10.593 20.5959 11.7414L19.5708 15.1969C19.1636 16.5702 18.914 17.412 18.4765 18.0966C17.7233 19.2751 16.5663 20.1386 15.2223 20.5256C14.4416 20.7503 13.5635 20.7502 12.1312 20.75H11.8688C10.4365 20.7502 9.55843 20.7503 8.77772 20.5256C7.43365 20.1386 6.2767 19.2751 5.52349 18.0966C5.08598 17.412 4.83637 16.5702 4.42921 15.1969L3.40431 11.742C3.06357 10.5934 2.77073 9.60621 2.54431 8.75001H2C1.58579 8.75001 1.25 8.41422 1.25 8.00001C1.25 7.5858 1.58579 7.25001 2 7.25001H4.93198L8.71231 3.46969C9.0052 3.17679 9.48008 3.17679 9.77297 3.46969C10.0659 3.76258 10.0659 4.23745 9.77297 4.53035L7.05331 7.25001H17.1894L14.4697 4.53033C14.1768 4.23744 14.1768 3.76256 14.4697 3.46967ZM10.75 12C10.75 11.5858 10.4142 11.25 10 11.25C9.58579 11.25 9.25 11.5858 9.25 12V16C9.25 16.4142 9.58579 16.75 10 16.75C10.4142 16.75 10.75 16.4142 10.75 16V12ZM14.75 12C14.75 11.5858 14.4142 11.25 14 11.25C13.5858 11.25 13.25 11.5858 13.25 12V16C13.25 16.4142 13.5858 16.75 14 16.75C14.4142 16.75 14.75 16.4142 14.75 16V12Z" fill="white"/>
                                    </svg>
                                @endif
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
