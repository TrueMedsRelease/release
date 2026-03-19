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
        <p style="margin-bottom: 0"><b>{{random_int(2, 30)}}{{__('text.common_product1')}}</b>{{__('text.common_product2')}}</p>
        </div>
    </div>
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
                            @if ($product['id'] == 616)
                                <img loading="lazy" src="{{ asset($design . '/images/gift-card.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="width: auto; height: auto; max-width: 20rem; max-height: 20rem;">
                            @endif
                        </picture>
                        {{-- <picture>
                            @if ($product['id'] != 616)
                                <source srcset="{{ asset('images/' . $product['image'] . '.webp') }}" type="image/webp">
                            @endif
                            <img loading="lazy" src="{{ $product['id'] != 616 ? asset('images/' . $product['image'] . '.webp') : asset($design . '/images/gift-card.svg') }}"
                                alt="{{ $product['image'] }}">
                        </picture> --}}
                    </div>

                    @if ($product['id'] != 616)
                        @if (count($product['aktiv']) > 0)
                            <div class="info-panel__row">
                                {!!__('text.product_active')!!}
                                @foreach ($product['aktiv'] as $aktiv)
                                    <a href="{{ route('home.active', $aktiv['url']) }}">{{ $aktiv['name'] }}</a>
                                @endforeach
                            </div>
                        @endif

                        <div class="info-panel__row">{!!__('text.product_pack1_1')!!}<b>{{__('text.product_pack2_1')}}{{ random_int(10, 40) }}{{__('text.product_pack3_1')}}</b></div>

                    @endif

                    <div class="info-panel__row">{{ $product['desc'] }}</div>

                    @if (count($product['disease']) > 0)
                        <div class="info-panel__row">
                            {{__('text.product_diseases')}}
                            @foreach ($product['disease'] as $disease)
                                <a
                                    href="{{ route('home.disease', $disease['url']) }}">{{ ucfirst($disease['name']) }}</a>
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
                                    @if (count($product['analog']) > 10)<a href="#" class="more">view all</a>@endif
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
                                            @php
                                                $sinonim['url'] = str_replace('.', ',', $sinonim['url']);
                                            @endphp
                                           <a href = "{{ route('home.product', $sinonim['url']) }}">
                                                {{ $sinonim['name'] }}
                                            </a>
                                        @endforeach
                                    </span>
                                        @if (count($product['sinonim']) > 10)<a href="#" class="more">view all</a>@endif
                                </div>
                            @else
                                @foreach ($product['sinonim'] as $sinonim)
                                    @php
                                        $sinonim['url'] = str_replace('.', ',', $sinonim['url']);
                                    @endphp
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
                            @if ($product['id'] != 616)
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
                                    @if ($product['id'] != 616)
                                        <th width="14.2%">{{__('text.product_price_per_pill_title')}}</th>
                                    @endif
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
                                                class="product__info @if ($loop->iteration == 1 && $product['id'] != 616) product__info--sale @endif">
                                                <div class="product__quantity">
                                                    {{ "{$item['num']} {$product['product_types'][$item['type_id']]}" }}
                                                </div>
                                                @if ($product['id'] != 616)
                                                    @if ($item['price'] >= 300)
                                                        <div class="product__delivery">{{__('text.cart_free_express')}}</div>
                                                    @elseif($item['price'] < 300 && $item['price'] >= 200)
                                                        <div class="product__delivery">{{__('text.cart_free_regular')}}</div>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>

                                        @if ($product['id'] != 616)
                                            <td class="product__price-per-pill" data-caption="Per Pill:">{{ $Currency::convert(round($item['price'] / $item['num'], 2), false, true) }}</td>
                                        @endif

                                        <td class="product__price-wrapper" data-caption="Special Price:">
                                            @if ($loop->remaining != 1 && $product['id'] != 616)
                                                <div class="product__discount">
                                                    <s>{{ $Currency::convert($dosage['max_pill_price'] * $item['num'], true) }}</s>
                                                    -{{ abs(ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100)) }}%
                                                </div>
                                            @endif
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
                                        </td>
                                        <td class="product__button-wrapper">
                                            <form action="{{ route('cart.add', $item['id']) }}" method="post">
                                                @csrf
                                                <button class="button product__button" type="submit">
                                                    <span class="icon">
                                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                            <svg width="1em" height="1em" fill="currentColor">
                                                                <use href="{{ asset("$design/svg/icons/sprite.svg#cart") }}"></use>
                                                            </svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="1em" height="1em" fill="currentColor">
                                                                <defs>
                                                                    <clipPath id="cart_clip0">
                                                                    <rect width="20" height="20" fill="currentColor"/>
                                                                    </clipPath>
                                                                </defs>

                                                                <g clip-path="url(#cart_clip0)">
                                                                    <path d="M18.9275 3.3975C18.6931 3.1162 18.3996 2.88996 18.0679 2.73485C17.7363 2.57973 17.3745 2.49955 17.0083 2.5H3.535L3.5 2.2075C3.42837 1.59951 3.13615 1.03894 2.67874 0.632065C2.22133 0.225186 1.63052 0.000284828 1.01833 0L0.833333 0C0.61232 0 0.400358 0.0877974 0.244078 0.244078C0.0877974 0.400358 0 0.61232 0 0.833333C0 1.05435 0.0877974 1.26631 0.244078 1.42259C0.400358 1.57887 0.61232 1.66667 0.833333 1.66667H1.01833C1.22244 1.66669 1.41945 1.74163 1.57198 1.87726C1.72451 2.0129 1.82195 2.19979 1.84583 2.4025L2.9925 12.1525C3.11154 13.1665 3.59873 14.1015 4.36159 14.78C5.12445 15.4585 6.10988 15.8334 7.13083 15.8333H15.8333C16.0543 15.8333 16.2663 15.7455 16.4226 15.5893C16.5789 15.433 16.6667 15.221 16.6667 15C16.6667 14.779 16.5789 14.567 16.4226 14.4107C16.2663 14.2545 16.0543 14.1667 15.8333 14.1667H7.13083C6.61505 14.1652 6.11233 14.0043 5.69161 13.7059C5.27089 13.4075 4.95276 12.9863 4.78083 12.5H14.7142C15.6911 12.5001 16.6369 12.1569 17.3865 11.5304C18.1361 10.9039 18.6417 10.0339 18.815 9.0725L19.4692 5.44417C19.5345 5.08417 19.5198 4.71422 19.4262 4.36053C19.3326 4.00684 19.1623 3.67806 18.9275 3.3975Z" fill="currentColor"/>
                                                                    <path d="M5.83329 20.0006C6.75376 20.0006 7.49995 19.2544 7.49995 18.3339C7.49995 17.4134 6.75376 16.6672 5.83329 16.6672C4.91282 16.6672 4.16663 17.4134 4.16663 18.3339C4.16663 19.2544 4.91282 20.0006 5.83329 20.0006Z" fill="currentColor"/>
                                                                    <path d="M14.1667 20.0006C15.0871 20.0006 15.8333 19.2544 15.8333 18.3339C15.8333 17.4134 15.0871 16.6672 14.1667 16.6672C13.2462 16.6672 12.5 17.4134 12.5 18.3339C12.5 19.2544 13.2462 20.0006 14.1667 20.0006Z" fill="currentColor"/>
                                                                </g>
                                                            </svg>
                                                        @endif
                                                    </span>
                                                    @if (!in_array(App::currentLocale(), ['de', 'it', 'gr', 'nl', 'hu', 'pt']))
                                                        <span class="button__text">{{__('text.product_add_to_cart_text_d2')}}</span>
                                                    @endif
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
                @if ($product['full_desc'])
                    {!! $product['full_desc'] !!}
                @endif

                @if ($product['id'] == 616)
                    <p style="margin: 0;">
                        <strong>{{__('text.gift_card_title')}}</strong>
                        <br>
                        <br>
                        <ol style="padding-left: 20px; line-height: 20px;">
                            <li style="margin-bottom: 15px;">{{__('text.gift_card_text1')}}</li>
                            <li>{{__('text.gift_card_text2')}}</li>
                        </ol>
                    </p>
                @endif

                <h2>{{__('text.recc_text')}}</h2>
                <div class="product-cards">
                    @foreach ($recommendation as $product_data)
                        @if ($loop->iteration == 7)
                            @break
                        @endif
                        <article class="card product-card">
                            @if ($product_data['discount'] != 0)
                                <span class="card__label">-{{ $product_data['discount'] }}%</span>
                            @endif
                            <a class="product-card__img" href="{{ route('home.product', $product_data['url']) }}">
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}">
                                </picture>
                            </a>
                            <h2 class="product-card__heading">
                                <a class="product-card__brand link-primary" href="{{ route('home.product', $product_data['url']) }}">{{ $product_data['name'] }}</a>
                            </h2>
                            <div class="product-card__active">
                                @foreach ($product_data['aktiv'] as $aktiv)
                                    <a class="product-card__ingredient" href="{{ route('home.active', $aktiv['url']) }}">{{ $aktiv['name'] }}</a>
                                @endforeach
                            </div>
                            <a class="product-card__text link-primary" href="{{ route('home.product', $product_data['url']) }}">
                                {{ str()->limit($product_data['desc'], 120, $end='...') }}
                            </a>
                            <div class="product-card__controls">
                                <button class="button product-card__button" aria-label="{{__('text.common_buy_button')}}" onclick="location.href='{{ route('home.product', $product_data['url']) }}'">
                                    <span class="icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset("$design/svg/icons/sprite.svg#cart") }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="1em" height="1em" fill="currentColor">
                                                <defs>
                                                    <clipPath id="cart_clip0">
                                                    <rect width="20" height="20" fill="currentColor"/>
                                                    </clipPath>
                                                </defs>

                                                <g clip-path="url(#cart_clip0)">
                                                    <path d="M18.9275 3.3975C18.6931 3.1162 18.3996 2.88996 18.0679 2.73485C17.7363 2.57973 17.3745 2.49955 17.0083 2.5H3.535L3.5 2.2075C3.42837 1.59951 3.13615 1.03894 2.67874 0.632065C2.22133 0.225186 1.63052 0.000284828 1.01833 0L0.833333 0C0.61232 0 0.400358 0.0877974 0.244078 0.244078C0.0877974 0.400358 0 0.61232 0 0.833333C0 1.05435 0.0877974 1.26631 0.244078 1.42259C0.400358 1.57887 0.61232 1.66667 0.833333 1.66667H1.01833C1.22244 1.66669 1.41945 1.74163 1.57198 1.87726C1.72451 2.0129 1.82195 2.19979 1.84583 2.4025L2.9925 12.1525C3.11154 13.1665 3.59873 14.1015 4.36159 14.78C5.12445 15.4585 6.10988 15.8334 7.13083 15.8333H15.8333C16.0543 15.8333 16.2663 15.7455 16.4226 15.5893C16.5789 15.433 16.6667 15.221 16.6667 15C16.6667 14.779 16.5789 14.567 16.4226 14.4107C16.2663 14.2545 16.0543 14.1667 15.8333 14.1667H7.13083C6.61505 14.1652 6.11233 14.0043 5.69161 13.7059C5.27089 13.4075 4.95276 12.9863 4.78083 12.5H14.7142C15.6911 12.5001 16.6369 12.1569 17.3865 11.5304C18.1361 10.9039 18.6417 10.0339 18.815 9.0725L19.4692 5.44417C19.5345 5.08417 19.5198 4.71422 19.4262 4.36053C19.3326 4.00684 19.1623 3.67806 18.9275 3.3975Z" fill="currentColor"/>
                                                    <path d="M5.83329 20.0006C6.75376 20.0006 7.49995 19.2544 7.49995 18.3339C7.49995 17.4134 6.75376 16.6672 5.83329 16.6672C4.91282 16.6672 4.16663 17.4134 4.16663 18.3339C4.16663 19.2544 4.91282 20.0006 5.83329 20.0006Z" fill="currentColor"/>
                                                    <path d="M14.1667 20.0006C15.0871 20.0006 15.8333 19.2544 15.8333 18.3339C15.8333 17.4134 15.0871 16.6672 14.1667 16.6672C13.2462 16.6672 12.5 17.4134 12.5 18.3339C12.5 19.2544 13.2462 20.0006 14.1667 20.0006Z" fill="currentColor"/>
                                                </g>
                                            </svg>
                                        @endif
                                    </span> <span class="button__text">{{__('text.common_buy_button')}}</span>
                                </button>
                            <div class="product-card__price">{{ $Currency::convert($product_data['price'], false, true) }}</div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
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
