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
                                            ${{ round($item['price'] / $item['num'], 2) }}</td>
                                        <td class="product__price-wrapper" data-caption="Special Price:">
                                            @if ($loop->remaining != 1 && $product['image'] != 'gift-card')
                                                <div class="product__discount">
                                                    <s>${{ $dosage['max_pill_price'] * $item['num'] }}</s>
                                                    -{{ ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) }}%
                                                </div>
                                            @endif
                                            <div class="product__price">{!!__('text.product_only')!!} ${{ $item['price'] }}</div>
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
