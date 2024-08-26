@extends($design . '.layouts.main')

@section('title', $product['name'])

@section('content')
<div class="main">
    <script>
    flagp = true;
    </script>
    <section class="product">
        <div class="product__wrapper">
            <div class="product-about">
                <h2 class="product-about__title" id = "scroll">
                    {{ $product['name'] }}
                </h2>
                <picture class = "product_center">
                    @if ($product['image'] != 'gift-card')
                        <source srcset="{{ asset('images/' . $product['image'] . '.webp') }}" type="image/webp">
                    @endif
                        <img src="{{ $product['image'] != 'gift-card' ? asset('images/' . $product['image'] . '.webp') : asset($design . '/images/gift_card_img.svg') }}"
                            alt="{{ $product['image'] }}">

                </picture>
                <ul class="product-about__characteristics">
                    @if ($product['image'] != 'gift-card')
                        @if (count($product['aktiv']) > 0)
                            <li class="product-about__characteristic_">
                                <span class="product-about__characteristic-name">
                                    {!!__('text.product_active')!!}
                                </span>
                                <ul>
                                    <li>
                                        @foreach ($product['aktiv'] as $aktiv)
                                            <a href="{{ route('home.active', $aktiv) }}" class="product-about__characteristic-meaning product-about__characteristic-meaning--link">
                                                {{ $aktiv }}
                                            </a>
                                        @endforeach
                                    </li>
                                </ul>
                            </li>
                        @endif

                        <li class="product-about__characteristic_">
                            <span class="product-about__characteristic-name">
                                {!!__('text.product_pack1_1')!!}
                            </span>
                            <span class="product-about__characteristic-meaning" style="color: #f2d43a;">
                                {{__('text.product_pack2_1')}}{{ random_int(10, 40) }}{{__('text.product_pack3_1')}}
                            </span>
                        </li>
                    @endif

                    <div class="product-about__text">
                        <p>
                            {{ $product['desc'] }}
                        </p>
                    </div>

                    @if (count($product['disease']) > 0)
                        <li class="product-about__characteristic">
                            <span class="product-about__characteristic-name">
                                {{__('text.product_diseases')}}
                            </span>
                            <ul>
                                <li>
                                    @foreach ($product['disease'] as $disease)
                                        <a href="{{ route('home.disease', str_replace(' ', '-', $disease)) }}" class="product-about__characteristic-meaning product-about__characteristic-meaning--link">
                                            {{ ucfirst($disease) }}
                                        </a>
                                    @endforeach
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if (!empty($product['analog']))
                        <li class="product-about__characteristic">
                            <span class="product-about__characteristic-name">
                                {{ $product['name'] }} {!!__('text.product_analogs')!!}
                            </span>
                            <ul>
                                <li>
                                    @foreach ($product['analog'] as $analog)
                                        <a href="{{ route('home.product', $analog['url']) }}" class="product-about__characteristic-meaning--link">
                                            {{ $analog['name'] }}
                                        </a>
                                    @endforeach
                                </li>
                            </ul>
                        </li>
                    @endif
                    @if ($product['image'] != 'gift-card')
                        @if (!empty($product['sinonim']))
                            <li class="product-about__characteristic">
                                <span class="product-about__characteristic-name">
                                    {{ $product['name'] }} {!!__('text.product_others')!!}
                                </span>
                                <ul>
                                    <li>
                                        @foreach ($product['sinonim'] as $sinonim)
                                            <a href = "" class="product-about__characteristic-meaning--link">
                                                {{ $sinonim }}
                                            </a>
                                        @endforeach
                                    </li>
                                </ul>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
            <div class="product__info">
            @foreach ($product['packs'] as $key => $dosage)
                    <ul class="product__info-items">
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
                                </li>
                            @endif
                            @if ($key != $prev_dosage)
                                <li class="product__info-item">
                                    <h3 class="product__info-title">
                                        {{ "{$product['name']} $key" }}
                                    </h3>
                                    <table class="product-table">
                                        <tbody>
                                        <tr class="product-table__list product-table__list--top">
                                            <th class="product-table__package">{{__('text.product_quantity_title')}}</th>
                                            <th class="product-table__per">{{__('text.product_price_per_pill_title')}}</th>
                                            <th class="product-table__price">{{__('text.product_price_title')}}</th>
                                            <th></th>
                                        </tr>
                                        @php
                                            $prev_dosage = $key;
                                        @endphp
                            @endif
                                <tr class="product-table__list">
                                    <th class="product-table__package">
                                        {{ "{$item['num']} {$product['type']}" }}
                                        @if ($product['image'] != 'gift-card')
                                            @if ($item['price'] >= 300)
                                                <span class="product-table__prompt">{{__('text.cart_free_express')}}</span>
                                            @elseif($item['price'] < 300 && $item['price'] >= 200)
                                                <span class="product-table__prompt">{{__('text.cart_free_regular')}}</span>
                                            @endif
                                        @endif
                                    </th>
                                    <th class="product-table__per">${{ round($item['price'] / $item['num'], 2) }}</th>
                                    <th class="product-table__price">
                                        @if ($loop->remaining != 1 && $product['image'] != 'gift-card')
                                            <span class="product-table__old">${{ $dosage['max_pill_price'] * $item['num'] }}</span>
                                            <span class="product-table__discount">-{{ ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) }}%</span>
                                        @endif
                                        <span class="product-table__current">@if ($product['image'] != 'gift-card'){!!__('text.product_only')!!} @endif ${{ $item['price'] }}</span>
                                    </th>
                                    <th class="product-table__add">
                                        <button class="product-table__btn" type="button" onclick="location.href = add_pack({{ $item['id'] }})">
                                            <span class="sr-only">{{__('text.product_add_to_cart_text_d2')}}</span>
                                        </button>
                                        <button class="product-table__cart" type="button" onclick="location.href = add_pack({{ $item['id'] }})">
                                            {{__('text.product_add_to_cart_text_d2')}}
                                        </button>
                                    </th>
                                </tr>
                        @endforeach
                                </tbody>
                            </table>
                        </li>
                    </ul>
            @endforeach
                    <div class="product__info-content">
                        @if ($product['full_desc'])
                            {!! $product['full_desc'] !!}
                        @endif
                    </div>
                </div>
        </div>
    </section>
    </div>
@endsection
