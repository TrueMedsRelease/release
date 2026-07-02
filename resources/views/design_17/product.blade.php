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

<div class="thread-chat">
    <div class="thread-chat__container">
        <div class="thread-chat__messages">
            <div class="chat-row chat-row--user">
                <div class="chat-message">
                    <div class="chat-message__content content" style="flex-direction: row; gap: 10px;">
                        {{-- @foreach ($product['categories'] as $category) --}}
                            {{-- <div class="chat-message__bubble">
                                <a class="product-info__use" href="{{ route('home.category', $category['url']) }} ">{{ $category['name'] }}</a>
                            </div> --}}
                            {{-- <div class="chat-message__bubble">
                                {{ $category['name'] }}
                            </div> --}}
                        {{-- @endforeach --}}
                    </div>
                </div>
            </div>
            <div class="chat-row chat-row--agent">
                <div class="chat-message">
                    <div class="chat-message__content content">
                        <span>{{ $product['desc'] }}</span>
                    </div>
                </div>
            </div>
            <div class="chat-row chat-row--page">
                <div class="chat-message">
                    <div class="chat-message__content content"></div>
                    <div class="chat-message__page">
                        <div class="product-card">
                            <div class="product-card__image">
                                @if ($product['id'] == 616)
                                    <picture>
                                        <source type="image/webp"
                                        srcset="{{ asset("$design/img/products/gift-product-175w.webp") }} 1x, {{ asset("$design/img/products/gift-product-350w.webp 2x") }}"><img
                                        src="{{ asset("$design/img/products/gift-product-175w.jpg") }}"
                                        srcset="{{ asset("$design/img/products/gift-product-175w.jpg") }} 1x, {{ asset("$design/img/products/gift-product-350w.jpg 2x") }}" width="200"
                                        height="200" alt="Gift">
                                    </picture>
                                @else
                                    <picture style="max-height: 200px; max-width: 200px;">
                                        <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                        <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="max-height: 200px; max-width: 200px;">
                                    </picture>
                                @endif
                            </div>
                            <div class="product-card__content">
                                <div class="product-card__name h1">{{ $product['name'] }}</div>
                                <div class="product-card__description">
                                    @if ($product['id'] != 616)
                                        @if (count($product['product_dosages']) > 0)
                                            <p class="product-info__ingredient">
                                                @foreach ($product['product_dosages'] as $aktiv)
                                                    {{ $aktiv }}
                                                @endforeach
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        @foreach ($product['packs'] as $key => $dosage)
                            <div class="panel">
                                <div class="panel__header">
                                    @if ($product['id'] != 616)
                                        @if (in_array($product['id'], [619, 620, 483, 484, 501, 615]))
                                            <h2 class="h2">{{ $product['name'] }}</h2>
                                        @else
                                            <h2 class="h2">{{ "{$product['name']} $key" }}@if ($loop->iteration == 1 && $product['rec_name'] != 'none'), {{__('text.product_need_more')}} <a href="{{ route('home.product', $product['rec_url']) }}">{{ $product['rec_name'] }}</a>@endif </h2>
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
                                                        <div class="product__quantity">{{ "{$item['num']} {$product['product_types'][$item['type_id']]}" }}</div>
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
                                                            <button class="button product__button">
                                                                <span class="icon">
                                                                    <svg width="1em" height="1em" fill="currentColor">
                                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#cart-white") }}"></use>
                                                                    </svg>
                                                                </span>
                                                                <span class="button__text">{{ __('text.product_add_to_cart_text_d2') }}</span>
                                                            </button>
                                                        @else
                                                            <button class="button product__button" style="padding: 0;">
                                                                <span class="icon" style="display: flex; width: 4rem; justify-content: center;">
                                                                    <svg width="1em" height="1em" fill="currentColor">
                                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#cart-white") }}"></use>
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
                            <div class="product-content content">
                                {!! $product['full_desc'] !!}
                            </div>
                        @endif

                        @if ($product['id'] == 616)
                            <div class="product-content content">
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

                        {{-- <div class="related-products">
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
                                    </article>
                                @endforeach
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection