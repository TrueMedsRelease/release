@extends($design . '.layouts.main')

@section('title', $product['name'])

@section('content')
    <div class="container page-wrapper">
        <main class="main main--grid main--aside-xl main_product">
            <div class="product_head" style="grid-column: span 2">
                <h1>
                    <div>{{ $product['name'] }}</div>
                </h1>
                <div class="product_category">
                    @foreach ($product['categories'] as $category)
                        <a href=" {{ route('home.category', $category['url']) }} ">{{ $category['name'] }}</a> <br>
                    @endforeach
                </div>
            </div>
            <aside class="main__aside">
                <div class="info-panel panel">
                    <div class="info-panel__image">
                        <picture>
                            @if ($product['image'] != 'gift-card')
                                <source srcset="{{ asset('images/' . $product['image'] . '.webp') }}" type="image/webp">
                            @endif
                            <img src="{{ $product['image'] != 'gift-card' ? asset('images/' . $product['image'] . '.webp') : asset($design . '/images/products/gift-card.svg') }}"
                                alt="{{ $product['image'] }}">
                        </picture>
                    </div>

                    <div class="info-panel__row">
                        {!!__('text.product_active')!!}
                        @foreach ($product['aktiv'] as $aktiv)
                            <a href="{{ route('home.active', $aktiv) }}">{{ $aktiv }}</a>
                        @endforeach
                    </div>

                    <div class="info-panel__row">{!!__('text.product_pack1_1')!!}<b>{{__('text.product_pack2_1')}}{{ random_int(10, 40) }}{{__('text.product_pack3_1')}}</b></div>

                    <div class="info-panel__row">{{ $product['desc'] }}</div>

                    @if (count($product['disease']) > 0)
                        <div class="info-panel__row">
                            {{__('text.product_diseases')}}
                            @foreach ($product['disease'] as $disease)
                                <a
                                    href="{{ route('home.disease', str_replace(' ', '-', $disease)) }}">{{ ucfirst($disease) }}</a>
                            @endforeach
                        </div>
                    @endif


                    @if (!empty($product['analog']))
                        <div class="info-panel__row">
                            {{ $product['name'] }} {!!__('text.product_analogs')!!}
                            @foreach ($product['analog'] as $analog)
                                <a href="{{ route('home.product', $analog['url']) }}"
                                    class="analog">{{ $analog['name'] }}</a>
                            @endforeach
                        </div>
                    @endif

                    @if (!empty($product['sinonim']))
                        <div class="info-panel__row">
                            {{ $product['name'] }} {!!__('text.product_others')!!}
                            @foreach ($product['sinonim'] as $sinonim)
                                <a href = "" class="others">{{ $sinonim }}</a>
                            @endforeach
                        </div>
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
            </aside>

            <div class="main__content">
                @foreach ($product['packs'] as $key => $dosage)
                    <div class="panel product-panel">
                        <h2 class="h2">{{ "{$product['name']} $key" }} {{-- , Need more? <a href="#!">Viagra Extra Dosage</a> --}}</h2>
                        <table class="table product-table">
                            <thead>
                                <tr>
                                    <th width="39.3%">{{__('text.product_package_title')}}</th>
                                    <th width="14.2%">{{__('text.product_price_per_pill_title')}}</th>
                                    <th width="20.4%">{{__('text.product_price_title')}}</th>
                                    <th width="26.1%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dosage as $item)
                                    @if ($loop->last)
                                        @continue
                                    @endif
                                    <tr class="product">
                                        <td class="product__info-wrapper" data-caption="Package:">
                                            <div
                                                class="product__info @if ($loop->iteration == 1) product__info--sale @endif">
                                                <div class="product__quantity">{{ "{$item['num']} {$product['type']}" }}
                                                </div>
                                                @if ($item['price'] >= 300)
                                                    <div class="product__delivery">{{__('text.cart_free_express')}}</div>
                                                @elseif($item['price'] < 300 && $item['price'] >= 200)
                                                    <div class="product__delivery">{{__('text.cart_free_regular')}}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="product__price-per-pill" data-caption="Per Pill:">
                                            {{ $Currency::convert(round($item['price'] / $item['num'], 2)) }}</td>
                                        <td class="product__price-wrapper" data-caption="Special Price:">
                                            @if ($loop->remaining != 1 && $product['image'] != 'gift-card')
                                                <div class="product__discount">
                                                    <s>{{ $Currency::convert($dosage['max_pill_price'] * $item['num'], true) }}</s>
                                                    -{{ ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) }}%
                                                </div>
                                            @endif
                                            <div class="product__price">Only {{ $Currency::convert($item['price'], true) }}</div>
                                        </td>
                                        <td class="product__button-wrapper">
                                            <form action="{{ route('cart.add', $item['id']) }}" method="post">
                                                @csrf
                                                <button class="button product__button" type="submit">
                                                    <span class="icon">
                                                        <svg width="1em" height="1em" fill="currentColor">
                                                            <use href="{{ $design }}/svg/icons/sprite.svg#cart"></use>
                                                        </svg>
                                                    </span>
                                                    <span class="button__text">{{__('text.product_add_to_cart_text_d2')}}</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
            <div class="raw-content raw-content--small">
                {!! $product['full_desc'] !!}
            </div>
        </main>

    </div>
@endsection
