@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)
@section('title_2', __('text.main_best_selling_title'))
@section('content')
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
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </p>
                            </div>
                            <div class="item-product__price">{{ $Currency::convert($product['price'], false, true) }}</div>
                        </div>
                        @if ($product['id'] != 616 && $product['discount'] != 0)
                            <span class="card__label">-{{ $product['discount'] }}%</span>
                        @endif
                        <div class="item-product__image-ibg">
                            @if ($product['id'] == 616)
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                                </picture>
                            @endif
                            {{-- @if ($product['id'] != 616)
                                <img loading="lazy" src="{{ asset("images/" . $product['image'] . ".webp") }}" width="140" height="140" alt="{{ $product['name'] }}">
                            @else
                                <img loading="lazy" src="{{ asset("$design/images/gift_card_img.svg") }}">
                            @endif --}}
                        </div>
                    </div>
                    <button type="submit" class="item-product__button">
                        <svg width="24" height="20">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                        </svg>
                        <span>{{__('text.product_add_to_cart_text_d2')}}</span>
                    </button>
                </a>
            @endforeach
        </div>
    </div>
</div>
</div>

@endsection
