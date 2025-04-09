@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="products">
    <h2 class="products__title title">{{__('text.aktiv_aktiv_result_title')}} {{ ucwords(str_replace('-', ' ', $active)) }}</h2>
    <div class="products__items">
        @foreach ($products as $product)
            <div class="products__item item-product">
                @if ($product['image'] != 'gift-card' && $product['discount'] != 0)
                    <span class="card__label">-{{ $product['discount'] }}%</span>
                @endif
                <a href="{{ route('home.product', $product['url']) }}">
                    <div class="item-product__info">
                        <div class="item-product__image">
                            @if ($product['image'] == 'gift-card')
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                                </picture>
                            @endif
                            {{-- <img loading="lazy" src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}"> --}}
                        </div>
                        <div class="item-product__data">
                            <div class="item-product__name">{{ $product['name'] }}</div>
                            <div class="item-product__company">
                                @foreach ($product['aktiv'] as $aktiv)
                                    {{ $aktiv['name'] }}
                                @endforeach
                            </div>
                            <div class="item-product__bottom-row">
                                <div class="item-product__price">{{ $Currency::convert($product['price'], false, true) }}</div>
                                <a type="button" href="{{ route('home.product', $product['url']) }}" class="item-product__button button button--filled button--narrow">
                                    @if (!in_array(App::currentLocale(), ['de', 'it', 'gr', 'nl', 'hu', 'pt', 'es']))
                                        {{__('text.common_buy_button')}}
                                    @else
                                        <svg width="18.5" height="21.5">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                        </svg>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                    <p class="item-product__desrc">{{ $product['desc'] }}</p>
                </a>
            </div>
        @endforeach
    </div>
</div>

@endsection
