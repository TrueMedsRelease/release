@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<section class="page__bestsellers bestsellers">
    @foreach ($products as $category)
        <aside class="categories-sidebar">
            <div class="categories-sidebar__inner">
                <div data-spollers class="categories-sidebar__spollers spollers">
                    <div class="spollers__item">
                        <button type="button" data-spoller class="spollers__title _spoller-active">{{__('text.main_best_selling_title')}}</button>
                        <ul class="spollers__body main_bestsellers" id="main_bestsellers_body">
                            @foreach ($bestsellers as $bestseller)
                                <li class="spollers__item-list"><a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span style="font-size: 12px;">{{ $Currency::convert($bestseller['price'], false, true) }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    @foreach ($menu as $category_menu)
                        <div class="spollers__item">
                            <button type="button" data-spoller class="spollers__title @if($category['name'] == $category_menu['name']) _spoller-active @endif">{{ $category_menu['name'] }}</button>
                            <ul class="spollers__body" id="this_product_category">
                                @foreach ($category_menu['products'] as $item)
                                    <li class="spollers__item-list">
                                        <a href="{{ route('home.product', $item['url']) }}">
                                            {{ $item['name'] }}
                                        </a>
                                        <span style="font-size: 12px;">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>

        <div class="bestsellers__container">
            <div class="bonus_block">
                <div class="bonus1">
                    <img loading="lazy" src="{{ asset("$design/images/bonus1_1.png") }}">
                </div>
                <div class="bonus2">
                    <img loading="lazy" src="{{ asset("$design/images/bonus2_2.png") }}">
                </div>
            </div>
            <h2 class="bestsellers__title title">{{ $category['name'] }}</h2>
            <div class="bestsellers__body">
                <div class="product_list">
                    @foreach ($category['products'] as $product)
                        <div class="product_info">
                            <div class="product_info_top">
                                <a href="{{ route('home.product', $product['url']) }}">
                                    <div class="product_img">
                                        @if ($product['image'] == 'gift-card')
                                            <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                                        @else
                                            <picture>
                                                <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                                <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                                            </picture>
                                        @endif
                                        {{-- <img loading="lazy" src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}"> --}}
                                    </div>
                                </a>
                                <a href="{{ route('home.product', $product['url']) }}" class="product_center">
                                    <div class="product_main">
                                        <div class="product_text">
                                            <span class="product_name">{{ $product['name'] }}</span>
                                            <span class="product_active">
                                                @foreach ($product['aktiv'] as $aktiv)
                                                    {{ $aktiv['name'] }}
                                                @endforeach
                                            </span>
                                        </div>
                                        <div class="product_desc top">{{ $product['desc'] }}</div>
                                    </div>
                                </a>
                                <div class="product_right_block">
                                    <div class="product_price">{{ $Currency::convert($product['price'], false, true) }}</div>
                                    <button type="button" class="product-card__button button button--accent" title="{{__('text.product_add_to_cart_text')}}" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                                        <svg width="24" height="24">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart-white") }}"></use>
                                        </svg>
                                        <span>{{__('text.common_buy_button')}}</span>
                                    </button>
                                </div>
                            </div>
                            <div class="product_info_bottom">
                                <div class="product_desc bottom">{{ $product['desc'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</section>

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