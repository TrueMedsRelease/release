@extends($design . '.layouts.main')

@section('title', __('text.common_best_selling_title'))

@section('content')
<script>
    flagm = true;
    is_main = true;
</script>

<div class="products">
    <h2 class="products__title title">{{__('text.main_best_selling_title')}}</h2>
    <div class="products__items">
        @foreach ($bestsellers as $product)
            <div class="products__item item-product">
                <a href="{{ route('home.product', $product['url']) }}">
                <div class="item-product__info">
                    <div class="item-product__image">
                        <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" width="140" height="140" alt="{{ $product['name'] }}">
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
                            <a type="button" href="{{ route('home.product', $product['url']) }}" class="item-product__button button button--filled button--narrow">
                                {{-- {if !in_array($data.language.code, ['de', 'fr', 'it', 'es'])}
                                    {#buy_button#}
                                {else} --}}
                                    <svg width="18.5" height="21.5">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                    </svg>
                                {{-- {/if} --}}
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