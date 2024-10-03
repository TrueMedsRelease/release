@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@if (count($products) == 0)
    @section('title_3')
        <h4 class="page__title title">{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h4>
        <h2 class="page__title title">{{__('text.search_result_best_for_search')}}</h2>
    @endsection
@else
    @section('title_2', __('text.search_result_title_page') . ' «' . $search_text . '»')
@endif

@section('content')
@if (count($products) == 0)
    <div class="products">
        <h2 class="products__title title">{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h2>
        <h2 class="products__title title">{{__('text.search_result_best_for_search')}}</h2>
        <div class="products__items">
            @foreach ($bestsellers as $product)
                <div class="products__item item-product">
                    <a href="{{ route('home.product', $product['url']) }}">
                        <div class="item-product__info">
                            <div class="item-product__image">
                                @if ($product['image'] == 'gift-card')
                                    <img src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                                @else
                                    <picture>
                                        <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                        <img src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                                    </picture>
                                @endif
                                {{-- <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}"> --}}
                            </div>
                            <div class="item-product__data">
                                <div class="item-product__name">{{ $product['name'] }}</div>
                                <div class="item-product__company">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv }}
                                    @endforeach
                                </div>
                                <div class="item-product__bottom-row">
                                    <div class="item-product__price">${{ $product['price'] }}</div>
                                    <a type="button" href="{{ route('home.product', $product['url']) }}" class="item-product__button button button--filled button--narrow">{{__('text.common_buy_button')}}</a>
                                </div>
                            </div>
                        </div>
                        <p class="item-product__desrc">{{ $product['desc'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="products">
        <h2 class="products__title title" id="scroll">{{__('text.search_result_title_page')}}  «{{$search_text}}»</h2>
        <div class="products__items">
            @foreach ($products as $product)
                <div class="products__item item-product">
                    <a href="{{ route('home.product', $product['url']) }}">
                        <div class="item-product__info">
                            <div class="item-product__image">
                                @if ($product['image'] == 'gift-card')
                                    <img src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                                @else
                                    <picture>
                                        <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                        <img src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                                    </picture>
                                @endif
                                {{-- <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}"> --}}
                            </div>
                            <div class="item-product__data">
                                <div class="item-product__name">{{ $product['name'] }}</div>
                                <div class="item-product__company">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv }}
                                    @endforeach
                                </div>
                                <div class="item-product__bottom-row">
                                    <div class="item-product__price">{{ $Currency::convert($product['price'], false, true) }}</div>
                                    <a type="button" href="{{ route('home.product', $product['url']) }}" class="item-product__button button button--filled button--narrow">{{__('text.common_buy_button')}}</a>
                                </div>
                            </div>
                        </div>
                        <p class="item-product__desrc">{{ $product['desc'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif

@endsection
