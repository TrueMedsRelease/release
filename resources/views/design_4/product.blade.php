@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<script>
    flagp = true;
</script>
    <div class="content__top-line top-line" data-da=".page-product__image, 650, last">
        <h1 class="top-line__title title">{{ $product['name'] }}</h1>
        <span class="top-line__group">
            @foreach ($product['categories'] as $category)
                <a href="{{ route('home.category', $category['url']) }}">{{ $category['name'] }}</a>
            @endforeach
        </span>
    </div>
    <div class="page-product"  id="scroll">
        <aside class="page-product__aside">
            <div class="page-product__descr">
                <div class="page-product__image">
                    <div class="page-product__image-wrapper">
                        @if ($product['image'] == 'gift-card')
                            <img src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                        @else
                            <picture>
                                <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                <img src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                            </picture>
                        @endif
                        {{-- @if ($product['image'] != 'gift-card')
                            <picture>
                                <source srcset="{{ asset('images/' . $product['image'] . '.webp') }}" type="image/webp">
                                <img src="{{ asset('images/' . $product['image'] . '.webp') }}" alt="{{ $product['image'] }}">
                            </picture>
                        @else
                            <img src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                        @endif --}}
                    </div>
                </div>
                <div class="page-product__details details-page-product">
                    @if ($product['image'] != 'gift-card')
                        @if (count($product['aktiv']) > 0)
                            <p class="details-page-product__row">{!!__('text.product_active')!!}
                                @foreach ($product['aktiv'] as $aktiv)
                                    <a href="{{ route('home.active', $aktiv) }}">
                                        {{ $aktiv }}
                                    </a>
                                @endforeach
                            </p>
                        @endif

                        <p class="details-page-product__row">
                            {!!__('text.product_pack1_1')!!}
                            <b style="color: #f2d43a;">{{__('text.product_pack2_1')}}{{ random_int(10, 40) }}{{__('text.product_pack3_1')}}</b>
                        </p>
                    @endif

                    <p class="details-page-product__descr">{{ $product['desc'] }}</p>

                    @if (count($product['disease']) > 0)
                        @if (count($product['disease']) > 10)
                            <div class="details-page-product__block-links">
                                <h2 class="details-page-product__label">{{__('text.product_diseases')}}</h2>
                                <div class="details-page-product__links">
                                    @foreach ($product['disease'] as $disease)
                                        <a href="{{ route('home.disease', str_replace(' ', '-', $disease)) }}">
                                            {{ ucfirst($disease) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="details-page-product__block-links">
                                <h2 class="details-page-product__label">{{__('text.product_diseases')}}</h2>
                                <div class="details-page-product__links">
                                    @foreach ($product['disease'] as $disease)
                                        <a href="{{ route('home.disease', str_replace(' ', '-', $disease)) }}">
                                            {{ ucfirst($disease) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    @if (!empty($product['analog']))
                        @if (count($product['analog']) > 10)
                            <div class="details-page-product__block-links">
                                <h2 class="details-page-product__label">{{ $product['name'] }} {!!__('text.product_analogs')!!}</h2>
                                <div class="details-page-product__links">
                                    @foreach ($product['analog'] as $analog)
                                        <a href="{{ route('home.product', $analog['url']) }}">
                                            {{ $analog['name'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="details-page-product__block-links">
                                <h2 class="details-page-product__label">{{ $product['name'] }} {!!__('text.product_analogs')!!}</h2>
                                <div class="details-page-product__links">
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
                                <div class="details-page-product__block-links">
                                    <h2 class="details-page-product__label">{{ $product['name'] }} {!!__('text.product_others')!!}</h2>
                                    <div class="details-page-product__links">
                                        @foreach ($product['sinonim'] as $sinonim)
                                            <a href = "{{ route('home.product', $sinonim['url']) }}">
                                                {{ $sinonim['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="details-page-product__block-links">
                                    <h2 class="details-page-product__label">{{ $product['name'] }} {!!__('text.product_others')!!}</h2>
                                    <div class="details-page-product__links">
                                        @foreach ($product['sinonim'] as $sinonim)
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
        <div class="page-product__content">
                <div class="page-product__items">
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
                                <div class="page-product__item item-product-info">
                                    <h3 class="item-product-info__name">
                                        @if (!in_array($product['id'], [616, 619, 620, 483, 484, 501, 615]))
                                            {{ "{$product['name']} $key" }}@if ($loop->parent->iteration == 1 && $product['rec_name'] != 'none')<span style="font-weight:lighter;">, {{__('text.product_need_more')}}</span> <span class="details-page-product"><a href="{{route('home.product', $product['rec_url'])}}">{{ $product['rec_name'] }}</a></span> @endif
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
                                <tbody>
                            @endif
                        <tr class="item-product-info__row">
                            <th class="item-product-info__package">{{ "{$item['num']} {$product['type']}" }}
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
                                        @if (ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) == 0)
                                            {{ $Currency::convert($item['price']) }}
                                        @else
                                            <span>{!!__('text.product_only')!!}&nbsp;<span>{{ $Currency::convert($item['price']) }}</span>
                                        @endif
                                    @else
                                        {{ $Currency::convert($item['price']) }}
                                    @endif
                                </span>
                            </th>
                            <th class="item-product-info__btn">
                                <form method="POST" action="{{ route('cart.add', $item['id']) }}">
                                    @csrf
                                    <button type="submit" class="item-product-info__add-to-cart button button--filled">
                                        <span>
                                            {{-- {if !in_array($data.language.code, ['de', 'fr', 'it', 'es'])}
                                                {#add_to_cart_text_d2#}
                                            {else} --}}
                                                <svg width="18.5" height="21.5">
                                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                                </svg>
                                            {{-- {/if} --}}
                                        </span>
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
        </div>

        @if ($product['full_desc'])
            <div class="page-product__info info-product" style="font-weight: 300; line-height: 1.6923076923;">
                {!! $product['full_desc'] !!}
            </div>
        @endif

    </div>
</div>

@endsection
