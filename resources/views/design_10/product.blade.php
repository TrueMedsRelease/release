@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

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
                            @if ($product['image'] == 'gift-card')
                                <img src="{{ asset($design . '/images/gift-card.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                <img src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                            @endif
                        </picture>
                        {{-- <picture>
                            @if ($product['image'] != 'gift-card')
                                <source srcset="{{ asset('images/' . $product['image'] . '.webp') }}" type="image/webp">
                            @endif
                            <img src="{{ $product['image'] != 'gift-card' ? asset('images/' . $product['image'] . '.webp') : asset($design . '/images/gift-card.svg') }}"
                                alt="{{ $product['image'] }}">
                        </picture> --}}
                    </div>

                    @if ($product['image'] != 'gift-card')
                        <div class="info-panel__row">
                            {!!__('text.product_active')!!}
                            @foreach ($product['aktiv'] as $aktiv)
                                <a href="{{ route('home.active', $aktiv) }}">{{ $aktiv }}</a>
                            @endforeach
                        </div>

                        <div class="info-panel__row">{!!__('text.product_pack1_1')!!}<b>{{__('text.product_pack2_1')}}{{ random_int(10, 40) }}{{__('text.product_pack3_1')}}</b></div>

                    @endif

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
                            @if ($agent->IsMobile())
                                <div class="text-box">
                                    <span class="text">
                                        @foreach ($product['analog'] as $analog)
                                            <a href="{{ route('home.product', $analog['url']) }}"
                                                class="analog">{{ $analog['name'] }}</a>
                                        @endforeach
                                    </span>
                                    @if (count($product['analog']) > 10)<a href="#" class="more">view all</a>@endisset
                                </div>
                            @else
                                @foreach ($product['analog'] as $analog)
                                    <a href="{{ route('home.product', $analog['url']) }}"
                                        class="analog">{{ $analog['name'] }}</a>
                                @endforeach
                            @endif
                        </div>
                    @endif

                    @if (!empty($product['sinonim']))
                        <div class="info-panel__row">
                            {{ $product['name'] }} {!!__('text.product_others')!!}
                            @if ($agent->IsMobile())
                                <div class="text-box">
                                    <span class="text">
                                        @foreach ($product['sinonim'] as $sinonim)
                                           <a href = "{{ route('home.product', $sinonim['url']) }}">
                                                {{ $sinonim['name'] }}
                                            </a>
                                        @endforeach
                                    </span>
                                        @if (count($product['sinonim']) > 10)<a href="#" class="more">view all</a>@endisset
                                </div>
                            @else
                                @foreach ($product['sinonim'] as $sinonim)
                                    <a href = "{{ route('home.product', $sinonim['url']) }}">
                                        {{ $sinonim['name'] }}
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            </aside>

            <div class="main__content">
                @foreach ($product['packs'] as $key => $dosage)
                    <div class="panel product-panel">
                        <h2 class="h2">
                            @if ($product['image'] != 'gift-card')
                                @if (in_array($product['id'], [619, 620, 483, 484, 501, 615]))
                                    {{ $product['name'] }}
                                @else
                                    {{ "{$product['name']} $key" }}@if ($loop->iteration == 1 && $product['rec_name'] != 'none')<span style="font-weight:lighter;">, {{__('text.product_need_more')}}</span> <a href="{{route('home.product', $product['rec_url'])}}">{{ $product['rec_name'] }}</a> @endif
                                @endif
                            @else
                                {{ $product['name'] }}
                            @endif
                        </h2>
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
                                                class="product__info @if ($loop->iteration == 1 && $product['image'] != 'gift-card') product__info--sale @endif">
                                                <div class="product__quantity">
                                                    {{ "{$item['num']} {$product['type']}" }}
                                                </div>
                                                @if ($product['image'] != 'gift-card')
                                                    @if ($item['price'] >= 300)
                                                        <div class="product__delivery">{{__('text.cart_free_express')}}</div>
                                                    @elseif($item['price'] < 300 && $item['price'] >= 200)
                                                        <div class="product__delivery">{{__('text.cart_free_regular')}}</div>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td class="product__price-per-pill" data-caption="Per Pill:">
                                            {{ $Currency::convert(round($item['price'] / $item['num'], 2), false, true) }}</td>
                                        <td class="product__price-wrapper" data-caption="Special Price:">
                                            @if ($loop->remaining != 1 && $product['image'] != 'gift-card')
                                                <div class="product__discount">
                                                    <s>{{ $Currency::convert($dosage['max_pill_price'] * $item['num'], true) }}</s>
                                                    -{{ ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) }}%
                                                </div>
                                            @endif
                                            <div class="product__price">
                                                @if ($product['image'] != 'gift-card')
                                                    {{__('text.cart_only')}} {{ $Currency::convert($item['price'], true) }}
                                                @else
                                                   {{ $Currency::convert($item['price'], true) }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="product__button-wrapper">
                                            <form action="{{ route('cart.add', $item['id']) }}" method="post">
                                                @csrf
                                                <button class="button product__button" type="submit">
                                                    <span class="icon">
                                                        <svg width="1em" height="1em" fill="currentColor">
                                                            <use href="{{ asset("$design/svg/icons/sprite.svg#cart") }}"></use>
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
            @if ($product['full_desc'])
                <div class="raw-content raw-content--small">
                    {!! $product['full_desc'] !!}
                </div>
            @endif
        </main>
        <aside class="aside categories-sidebar">
            <div class="categories-sidebar__inner">
                <div data-spollers class="categories-sidebar__spollers spollers">
                    <div class="spollers__item">
                        <button type="button" data-spoller class="spollers__title _spoller-active">{{__('text.common_best_selling_title')}}</button>
                        <ul class="spollers__body main_bestsellers" id="main_bestsellers_body">
                            @foreach ($bestsellers as $bestseller)
                                <li class="spollers__item-list"><a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span style="font-size: 13px; color: var(--color-secondary);">{{ $Currency::convert($bestseller['price'], false, true) }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    @foreach ($menu as $category)
                        <div class="spollers__item">
                            <button type="button" data-spoller class="spollers__title @if($cur_category == $category['name']) _spoller-active @endif">{{ $category['name'] }}</button>
                            <ul class="spollers__body">
                                @foreach ($category['products'] as $item)
                                    <li class="spollers__item-list">
                                        <a href="{{ route('home.product', $item['url']) }}">
                                            {{ $item['name'] }}
                                        </a>
                                        <span style="font-size: 13px; color: var(--color-secondary);">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
@endsection

@section('rewies')
    <div class="footer-testimonials">
        <div class="testimonial card">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_1')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_1')}}</div>
        </div>
        <div class="testimonial card">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_7')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_7')}}</div>
        </div>
        <div class="testimonial card">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_13')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_13')}}</div>
        </div>
        <div class="testimonial card">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_17')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_17')}}</div>
        </div>
    </div>
@endsection
