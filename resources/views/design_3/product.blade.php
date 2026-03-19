@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="cmcmodal hidden">
    <div class="bloktext">
       <p><b>{{random_int(2, 30)}}{{__('text.common_product1')}}</b>{{__('text.common_product2')}}</p>
    </div>
</div>
<script>
    flagp = true;
</script>
<div class="page__body">
    <div class="page__top-line top-line" data-da=".product__image, 650, last">
        <h1 class="top-line__title">{{ $product['name'] }}</h1>
        <a class="top-line__group">
            @foreach ($product['categories'] as $category)
                <a class="top-line__group" href="{{ route('home.category', $category['url']) }}">{{ $category['name'] }}</a>
            @endforeach
        </a>
    </div>
    <div class="product">
        <aside class="product__aside">
            <div class="product__descr">
                <div class="product__image">
                    <div class="product__image-wrapper">
                        @if ($product['id'] == 616)
                            <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                        @else
                            <picture>
                                <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="width: auto; height: auto; max-width: 175px; max-height: 175px;">
                            </picture>
                        @endif
                    </div>
                </div>
                <div class="product__details details-product">
                    @if ($product['id'] != 616)
                        @if (count($product['aktiv']) > 0)
                            <p class="details-product__row">{!!__('text.product_active')!!}
                                @foreach ($product['aktiv'] as $aktiv)
                                    <a href="{{ route('home.active', $aktiv['url']) }}">
                                        {{ $aktiv['name'] }}
                                    </a>
                                @endforeach
                            </p>
                        @endif

                        <p class="details-product__row">
                            {!!__('text.product_pack1_1')!!}
                            <b style="color: #f2d43a;">{{__('text.product_pack2_1')}}{{ random_int(10, 40) }}{{__('text.product_pack3_1')}}</b>
                        </p>
                    @endif

                    <p class="details-product__descr">{{ $product['desc'] }}</p>

                    @if (count($product['disease']) > 0)
                        <div class="details-product__block-links">
                            <h2 class="details-product__label">{{__('text.product_diseases')}}</h2>
                            <div class="details-product__links">
                                @foreach ($product['disease'] as $disease)
                                    <a href="{{ route('home.disease', $disease['url']) }}">
                                        {{ ucfirst($disease['name']) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (!empty($product['analog']))
                        @if (count($product['analog']) > 10)
                            <div class="details-product__block-links">
                                <h2 class="details-product__label">{{ $product['name'] }} {!!__('text.product_analogs')!!}</h2>
                                <div class="details-product__links limiter">
                                    @foreach ($product['analog'] as $analog)
                                        <a href="{{ route('home.product', $analog['url']) }}">
                                            {{ $analog['name'] }}
                                        </a>
                                    @endforeach
                                    <div class="bottom"></div>
                                </div>
                                <label for="read-more-checker" class="read-more-button"></label>
                            </div>
                        @else
                            <div class="details-product__block-links">
                                <h2 class="details-product__label">{{ $product['name'] }} {!!__('text.product_analogs')!!}</h2>
                                <div class="details-product__links">
                                    @foreach ($product['analog'] as $analog)
                                        <a href="{{ route('home.product', $analog['url']) }}">
                                            {{ $analog['name'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    @if ($product['id'] != 616)
                        @if (!empty($product['sinonim']))
                            @if (count($product['sinonim']) > 10)
                                <div class="details-product__block-links">
                                    <h2 class="details-product__label">{{ $product['name'] }} {!!__('text.product_others')!!}</h2>
                                    <div class="details-product__links limiter">
                                        @foreach ($product['sinonim'] as $sinonim)
                                            @php
                                                $sinonim['url'] = str_replace('.', ',', $sinonim['url']);
                                            @endphp
                                            <a href = "{{ route('home.product', $sinonim['url']) }}">
                                                {{ $sinonim['name'] }}
                                            </a>
                                        @endforeach
                                        <div class="bottom"></div>
                                    </div>
                                    <label for="read-more-checker" class="read-more-button"></label>
                                </div>
                            @else
                                <div class="details-product__block-links">
                                    <h2 class="details-product__label">{{ $product['name'] }} {!!__('text.product_others')!!}</h2>
                                    <div class="details-product__links">
                                        @foreach ($product['sinonim'] as $sinonim)
                                            @php
                                                $sinonim['url'] = str_replace('.', ',', $sinonim['url']);
                                            @endphp
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

        <div class="product__content">
            <div class="product__items  item-product-info">
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
                        <div class="product__item">
                        <h3 class="item-product-info__name">
                            @if (!in_array($product['id'], [616, 619, 620, 483, 484, 501, 615]))
                                {{ "{$product['name']} $key" }}@if ($loop->parent->iteration == 1 && $product['rec_name'] != 'none')<span style="font-weight:lighter;">, {{__('text.product_need_more')}}</span> <span class="details-product__links"><a href="{{route('home.product', $product['rec_url'])}}">{{ $product['rec_name'] }}</a></span> @endif
                            @else
                                {{ $product['name'] }}
                            @endif
                        </h3>
                        <table class="item-product-info__table">
                        <thead>
                        <tr class="item-product-info__row item-product-info__row--top">
                            <th class="item-product-info__package">{{__('text.product_package_title')}}</th>
                            @if ($product['id'] != 616)
                                <th class="item-product-info__per-pill">{{__('text.product_price_per_pill_title')}}</th>
                            @endif
                            <th class="item-product-info__price">{{__('text.product_price_title')}}</th>
                            <th class="item-product-info__btn"></th>
                        </tr>
                        </thead>
                        @php
                            $prev_dosage = $key;
                        @endphp
                    @endif
                    <tbody @if ($loop->iteration == 1 && $product['id'] != 616) class="item-product-info__row--discount" @endif>
                    <tr class="item-product-info__row">
                        <th class="item-product-info__package">
                            {{ "{$item['num']} {$product['product_types'][$item['type_id']]}" }}
                            @if ($product['id'] != 616)
                                @if ($item['price'] >= 300)
                                    <span class="item-product-info__delivery">{{__('text.cart_free_express')}}</span>
                                @elseif($item['price'] < 300 && $item['price'] >= 200)
                                    <span class="item-product-info__delivery">{{__('text.cart_free_regular')}}</span>
                                @endif
                            @endif
                        </th>

                        @if ($product['id'] != 616)
                            <th class="item-product-info__per-pill">{{ $Currency::convert(round($item['price'] / $item['num'], 2), false, true) }}</th>
                        @endif

                        <th class="item-product-info__price">
                            @if ($loop->remaining != 1 && $product['id'] != 616)
                                <span class="item-product-info__old-price">
                                    <span>{{ $Currency::convert($dosage['max_pill_price'] * $item['num']) }}</span>
                                    <span>-{{ abs(ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100)) }}%</span>
                                </span>
                            @endif
                            <span class="item-product-info__new-price">
                                @if ($product['id'] != 616)
                                    @if ($loop->remaining != 1)
                                        {{__('text.cart_only')}} {{ $Currency::convert($item['price'], true) }}
                                    @else
                                        {{ $Currency::convert($item['price'], true) }}
                                    @endif
                                @else
                                    {{ $Currency::convert($item['price'], true) }}
                                @endif
                            </span>
                        </th>
                        <th class="item-product-info__btn">
                            <form method="POST" action="{{ route('cart.add', $item['id']) }}">
                                @csrf
                                <button type="submit" class="item-product-info__add-to-cart">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="20" height="20">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 20" width="20" height="20">
                                            <path fill="#000" fill-rule="evenodd" d="M2.451 2.025C2.136 2 1.728 2 1.111 2H1a1 1 0 1 1 0-2h.148c.57 0 1.054 0 1.454.03.421.032.822.101 1.213.277a3.5 3.5 0 0 1 1.482 1.255c.094.142.172.288.238.438h14.782c.51 0 .958 0 1.322.032.38.034.792.112 1.17.348a2.5 2.5 0 0 1 1.086 1.46c.118.43.074.847-.003 1.22-.073.359-.202.788-.349 1.277l-1.401 4.67-.043.143c-.201.674-.377 1.266-.745 1.725a3 3 0 0 1-1.219.907c-.546.22-1.163.22-1.866.218H9.176c-.452 0-.845 0-1.173-.025a3.032 3.032 0 0 1-1.037-.238 3 3 0 0 1-1.27-1.076 3.031 3.031 0 0 1-.405-.983c-.078-.32-.143-.708-.217-1.153L4.07 4.507c-.102-.609-.17-1.01-.245-1.318-.072-.295-.136-.431-.195-.52a1.5 1.5 0 0 0-.635-.538c-.097-.043-.242-.084-.545-.106ZM6.014 4l.024.142 1.003 6.02c.081.49.134.802.192 1.039.055.225.1.309.129.353a1 1 0 0 0 .423.358c.048.022.138.051.369.069.243.018.56.019 1.057.019h8.908c.943 0 1.131-.018 1.267-.073a1 1 0 0 0 .407-.302c.091-.115.163-.29.433-1.193l1.39-4.63c.162-.541.264-.884.317-1.143.042-.208.035-.28.033-.292a.5.5 0 0 0-.216-.29c-.01-.005-.078-.034-.29-.052C21.198 4 20.84 4 20.275 4H6.014Zm15.737.077h-.001.001Zm.215.289v.001-.001Z" clip-rule="evenodd"/>
                                            <path fill="#000" d="M7 18a2 2 0 1 1 4 0 2 2 0 0 1-4 0Zm9 0a2 2 0 1 1 4 0 2 2 0 0 1-4 0Z"/>
                                        </svg>
                                    @endif
                                    <span>{{__('text.product_add_to_cart_text_d2')}}</span>
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

        <div class="product__info info-product">
            @if ($product['full_desc'])
                {!! $product['full_desc'] !!}
            @endif
            @if ($product['id'] == 616)
                <p>
                    <strong>{{__('text.gift_card_title')}}</strong>
                    <br>
                    <br>
                    <ol style="padding-left: 20px; line-height: 20px;">
                        <li style="margin-bottom: 15px;">{{__('text.gift_card_text1')}}</li>
                        <li>{{__('text.gift_card_text2')}}</li>
                    </ol>
                </p>
            @endif
        </div>
        </div>
    </div>
</div>
</div>

<h2 class="page__title title" style="margin-top: 20px;">{{__('text.recc_text')}}</h2>
<div class="products__items product_rec">
    @foreach ($recommendation as $product_data)
        @if ($loop->iteration == 7)
            @break
        @endif
        <a href="{{ route('home.product', $product_data['url']) }}" class="item-product">
            <div class="item-product__content">
                <div class="item-product__top">
                    <div class="item-product__left">
                        <div class="item-product__name">{{ $product_data['name'] }}</div>
                        <p class="item-product__company">
                            @foreach ($product_data['aktiv'] as $aktiv)
                                {{ $aktiv['name'] }}
                            @endforeach
                        </p>
                    </div>
                    <div class="item-product__price">{{ $Currency::convert($product_data['price'], false, true) }}</div>
                </div>
                @if ($product_data['discount'] != 0)
                    <span class="card__label">-{{ $product_data['discount'] }}%</span>
                @endif
                <div class="item-product__image-ibg">
                    <picture>
                        <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                        <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}">
                    </picture>
                </div>
            </div>
            <button type="button" class="item-product__button" onclick="location.href='{{ route('home.product', $product_data['url']) }}'">
                @if (env('APP_PRINT_SPRITE', 1) == 1)
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 20" width="20" height="20">
                        <path fill="#000" fill-rule="evenodd" d="M2.451 2.025C2.136 2 1.728 2 1.111 2H1a1 1 0 1 1 0-2h.148c.57 0 1.054 0 1.454.03.421.032.822.101 1.213.277a3.5 3.5 0 0 1 1.482 1.255c.094.142.172.288.238.438h14.782c.51 0 .958 0 1.322.032.38.034.792.112 1.17.348a2.5 2.5 0 0 1 1.086 1.46c.118.43.074.847-.003 1.22-.073.359-.202.788-.349 1.277l-1.401 4.67-.043.143c-.201.674-.377 1.266-.745 1.725a3 3 0 0 1-1.219.907c-.546.22-1.163.22-1.866.218H9.176c-.452 0-.845 0-1.173-.025a3.032 3.032 0 0 1-1.037-.238 3 3 0 0 1-1.27-1.076 3.031 3.031 0 0 1-.405-.983c-.078-.32-.143-.708-.217-1.153L4.07 4.507c-.102-.609-.17-1.01-.245-1.318-.072-.295-.136-.431-.195-.52a1.5 1.5 0 0 0-.635-.538c-.097-.043-.242-.084-.545-.106ZM6.014 4l.024.142 1.003 6.02c.081.49.134.802.192 1.039.055.225.1.309.129.353a1 1 0 0 0 .423.358c.048.022.138.051.369.069.243.018.56.019 1.057.019h8.908c.943 0 1.131-.018 1.267-.073a1 1 0 0 0 .407-.302c.091-.115.163-.29.433-1.193l1.39-4.63c.162-.541.264-.884.317-1.143.042-.208.035-.28.033-.292a.5.5 0 0 0-.216-.29c-.01-.005-.078-.034-.29-.052C21.198 4 20.84 4 20.275 4H6.014Zm15.737.077h-.001.001Zm.215.289v.001-.001Z" clip-rule="evenodd"/>
                        <path fill="#000" d="M7 18a2 2 0 1 1 4 0 2 2 0 0 1-4 0Zm9 0a2 2 0 1 1 4 0 2 2 0 0 1-4 0Z"/>
                    </svg>
                @endif
                <span>{{__('text.product_add_to_cart_text')}}</span>
            </button>
        </a>
    @endforeach
</div>

</div>
</div>
</div>



@endsection
