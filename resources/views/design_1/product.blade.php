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
                {{-- <picture class="product_center">
                    @if ($product['image'] != 'gift-card')
                        <source srcset="{{ asset('images/' . $product['image'] . '.webp') }}" type="image/webp">
                    @endif
                    <img loading="lazy" class="product-about__img" src="{{ $product['image'] != 'gift-card' ? asset('images/' . $product['image'] . '.webp') : asset($design . '/images/gift_card_img.svg') }}"
                        alt="{{ $product['image'] }}">
                </picture> --}}
                <picture class="product_center">
                    @if ($product['image'] == 'gift-card')
                        <img loading="lazy" class="product-about__img" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                    @else
                        <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                        <img loading="lazy" class="product-about__img" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                    @endif
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
                                            <a href="{{ route('home.active', $aktiv['url']) }}" class="product-about__characteristic-meaning product-about__characteristic-meaning--link" >
                                                {{ $aktiv['name'] }}
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
                                        <a href="{{ route('home.disease', str_replace(' ', '-', $disease)) }}" class="product-about__characteristic-meaning product-about__characteristic-meaning--link" >
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
                                        <a class="product-about__characteristic-meaning--link" href="{{ route('home.product', $analog['url']) }}">
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
                                            @php
                                                $sinonim['url'] = str_replace('.', ',', $sinonim['url']);
                                            @endphp
                                            <a href = "{{ route('home.product', $sinonim['url']) }}" class="product-about__characteristic-meaning--link">
                                                {{ $sinonim['name'] }}
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
                                        @if (!in_array($product['id'], [616, 619, 620, 483, 484, 501, 615]))
                                            {{ "{$product['name']} $key" }}@if ($loop->parent->iteration == 1 && $product['rec_name'] != 'none')<span style="font-weight:lighter;">, {{__('text.product_need_more')}}</span> <a class="product-about__characteristic-meaning--link" href="{{route('home.product', $product['rec_url'])}}">{{ $product['rec_name'] }}</a> @endif
                                        @else
                                            {{ $product['name'] }}
                                        @endif
                                    </h3>
                                    <table class="product-table">
                                        <tbody>
                                        <tr class="product-table__list product-table__list--top">
                                            <th class="product-table__package">{{__('text.product_package_title')}}</th>
                                            <th class="product-table__per">{{__('text.product_price_per_pill_title')}}</th>
                                            <th class="product-table__price">{{__('text.product_price_title')}}</th>
                                            <th></th>
                                        </tr>
                                        @php
                                            $prev_dosage = $key;
                                        @endphp
                            @endif
                                        <tr class="product-table__list @if ($loop->iteration == 1 && $product['image'] != 'gift-card') product-table__list--badge @endif">
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
                                            <th class="product-table__per">{{ $Currency::convert(round($item['price'] / $item['num'], 2), false, true) }}</th>
                                            <th class="product-table__price">
                                                @if ($loop->remaining != 1 && $product['image'] != 'gift-card')
                                                    <span class="product-table__old">{{ $Currency::convert($dosage['max_pill_price'] * $item['num'], true) }}</span>
                                                    <span class="product-table__discount">-{{ ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) }}%</span>
                                                @endif

                                                @if ($product['image'] != 'gift-card')
                                                    @if (ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) == 0)
                                                        <span class="product-table__current--discount">{{ $Currency::convert($item['price'], true) }}</span>
                                                    @else
                                                        <span class="product-table__current">{{__('text.cart_only')}} {{ $Currency::convert($item['price'], true) }}</span>
                                                    @endif
                                                @else
                                                    <span class="product-table__current">{{ $Currency::convert($item['price'], true) }}</span>
                                                @endif
                                            </th>
                                            <th class="product-table__add">
                                                <form action="{{ route('cart.add', $item['id']) }}" method="post">
                                                    @csrf
                                                    <button class="product-table__btn" type="submit">
                                                        <span class="sr-only">{{__('text.product_add_to_cart_text')}}</span>
                                                    </button>
                                                    <button class="product-table__cart" type="submit">
                                                        <div>
                                                            <img src="{{ asset($design . '/images/icons/cart-white.webp') }}">
                                                        </div>
                                                        <div>
                                                            {{__('text.product_add_to_cart_text')}}
                                                        </div>
                                                    </button>
                                                </form>
                                            </th>
                                        </tr>
                                    @endforeach
                                        </tbody>
                                    </table>
                                </li>
                    </ul>
                @endforeach
                @if ($product['full_desc'])
                    <div class="product__info-content">
                        {!! $product['full_desc'] !!}
                    </div>
                @endif
                @if ($product['image'] == 'gift-card')
                    <div class="product__info-content">
                        <p>
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
            </div>
        </div>
    </section>
    <h4 class="best-sellers__title title" style="margin-top: 20px;">{{__('text.recc_text')}}</h2>
    <div class="best-sellers__items product_rec">
        @foreach ($recommendation as $product_data)
            @if ($loop->iteration == 7)
                @break
            @endif
            <a href="{{ route('home.product', $product_data['url']) }}" class="product-card">
                <div class="product-card__body">
                    <div class="product-card__top">
                        <div class="product-card__info">
                            <h3 class="product-card__name">{{ $product_data['name'] }}</h3>
                            <h4 class="product-card__company">
                                @foreach ($product_data['aktiv'] as $aktiv)
                                    {{ $aktiv['name'] }}
                                @endforeach
                            </h4>
                        </div>
                        <div class="product-card__price">{{ $Currency::convert($product_data['price'], false, true) }}</div>
                    </div>
                    @if ($product_data['discount'] != 0)
                        <span class="card__label">-{{ $product_data['discount'] }}%</span>
                    @endif
                    <div class="product-card__image">
                        <picture>
                            <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                            <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['image'] }}">
                        </picture>
                    </div>
                </div>
                <button type="button" class="product-card__button button button--primary">
                    <picture><source srcset="{{ asset("$design/images/icons/cart.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/icons/cart.png") }}" width="23" height="23" alt=""></picture>
                </button>
            </a>
        @endforeach
    </div>
</div>

@endsection
