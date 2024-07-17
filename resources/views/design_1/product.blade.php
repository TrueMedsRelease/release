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
                        <a href="#!">{{ $category }}</a> <br>
                    @endforeach
                </div>
            </div>
            <aside class="main__aside">
                <div class="info-panel panel">
                    <div class="info-panel__image">
                        <picture>
                            <source srcset="{{ asset('images/' . $product['image'] . '.webp') }}" type="image/webp">
                            <img src="{{ asset('images/' . $product['image'] . '.webp') }}" alt="viagra">
                        </picture>
                    </div>

                    <div class="info-panel__row">
                        Active Ingredient:
                        @foreach ($product['aktiv'] as $aktiv)
                            <a href="">{{ $aktiv }}</a>
                        @endforeach
                    </div>

                    <div class="info-panel__row">In Stock:<b>Only {{ random_int(10, 40) }} packs left</b></div>

                    <div class="info-panel__row">{{ $product['desc'] }}</div>

                    @if (count($product['disease']) > 0)
                        <div class="info-panel__row">
                            Diseases:
                            @foreach ($product['disease'] as $disease)
                                <a href="#!">{{ ucfirst($disease) }}</a>
                            @endforeach
                        </div>
                    @endif


                    @if (!empty($product['analog']))
                        <div class="info-panel__row">
                            {{ $product['name'] }} analogs:
                            @foreach ($product['analog'] as $analog)
                                <a href="{{ route('home.product', $analog['url']) }}"
                                    class="analog">{{ $analog['name'] }}</a>
                            @endforeach
                        </div>
                    @endif

                    @if (!empty($product['sinonim']))
                        <div class="info-panel__row">
                            {{ $product['name'] }} other names:
                            @foreach ($product['sinonim'] as $sinonim)
                                <a href = "{{ route('home.product', $sinonim) }}" class="others">{{ $sinonim }}</a>
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
                                    <th width="39.3%">Package</th>
                                    <th width="14.2%">Per Pill</th>
                                    <th width="20.4%">Special Price</th>
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
                                        <div class="product__info @if ($loop->iteration == 1) product__info--sale @endif">
                                            <div class="product__quantity">{{ "{$item['num']} {$product['type']}" }}</div>
                                            @if ($item['price'] >= 300)
                                            <div class="product__delivery">Free Express Delivery</div>
                                            @elseif($item['price'] < 300 && $item['price'] >= 200)
                                            <div class="product__delivery">Free Regular Delivery</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="product__price-per-pill" data-caption="Per Pill:">{{ round(($item['price'] / $item['num']),2) }}</td>
                                    <td class="product__price-wrapper" data-caption="Special Price:">
                                        @if ($loop->remaining != 1)
                                        <div class="product__discount"><s>${{ $dosage['max_pill_price'] * $item['num'] }}</s>
                                            -{{ ceil(100 - (($item['price']/($dosage['max_pill_price'] * $item['num']))*100)) }}%
                                        </div>
                                        @endif
                                        <div class="product__price">Only ${{ $item['price'] }}</div>
                                    </td>
                                    <td class="product__button-wrapper"><button class="button product__button"><span
                                                class="icon"><svg width="1em" height="1em" fill="currentColor">
                                                    <use href="{{ $design }}/svg/icons/sprite.svg#cart"></use>
                                                </svg></span> <span class="button__text">Add</span></button></td>
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
