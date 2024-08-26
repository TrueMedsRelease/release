@extends($design . '.layouts.main')

@section('title', $search_text)

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
    <div class="page__products products">
        <div class="products__items">
            @foreach ($bestsellers as $product)
                <a href="{{ route('home.product', $product['url']) }}" class="item-product">
                    <div class="item-product__content">
                        <div class="item-product__top">
                            <div class="item-product__left">
                                <div class="item-product__name">{{ $product['name'] }}</div>
                                <p class="item-product__company">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv }}
                                    @endforeach
                                </p>
                            </div>
                            <div class="item-product__price">${{ $product['price'] }}</div>
                        </div>
                        <div class="item-product__image-ibg">
                            <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}">
                        </div>
                    </div>
                    <button type="button" class="item-product__button" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                        <svg width="24" height="20">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                        </svg>
                        <span>{{__('text.product_add_to_cart_text')}}</span>
                    </button>
                </a>
            @endforeach
        </div>
    </div>
    </div>
@else
    <div class="page__products products">
        <div class="products__items">
            @foreach ($products as $product)
                <a href="{{ route('home.product', $product['url']) }}" class="item-product">
                    <div class="item-product__content">
                        <div class="item-product__top">
                            <div class="item-product__left">
                                <div class="item-product__name">{{ $product['name'] }}</div>
                                <p class="item-product__company">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv }}
                                    @endforeach
                                </p>
                            </div>
                            <div class="item-product__price">${{ $product['price'] }}</div>
                        </div>
                        <div class="item-product__image-ibg">
                            <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}">
                        </div>
                    </div>
                    <button type="button" class="item-product__button" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                        <svg width="24" height="20">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                        </svg>
                        <span>{{__('text.product_add_to_cart_text')}}</span>
                    </button>
                </a>
            @endforeach
        </div>
    </div>
    </div>
@endif

</div>

@endsection
