@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@if (count($products) == 0)
    @section('title_3')
        <h4 class="page__title title">{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h4>
        <h2 class="page__title title">{{__('text.search_result_best_for_search')}}</h2>
    @endsection
@else
    @section('title_2', __('text.search_result_title_page') . ' «' . $search_text . '»')
@endif

@section('content')
@if (count($products) == 0)
    <div class="products">
        <h2 class="products__title title no_product_head" style="margin-bottom: 20px">{{ __("text.common_product_text") }} «{{ $search_text }}» {{ __("text.search_not_found") }}</h2>
        <div class="no_product_text" style="margin-bottom: 10px">{{ __("text.search_not_carry") }} «{{ $search_text }}» {{ __("text.search_this_time") }}</div>
        <div class="no_product_text" style="margin-bottom: 20px">{{ __("text.search_product_request") }}</div>
        <div class="button" id="go_to_contact_us" onclick="location.href = '{{ route('home.contact_us') }}'">
            {{ __("text.common_contact_us_main_menu_item") }}
        </div>
    </div>
    {{-- <div class="products">
        <h2 class="products__title title">{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h2>
        <h2 class="products__title title">{{__('text.search_result_best_for_search')}}</h2>
        <div class="products__items">
            @foreach ($bestsellers as $product)
                <div class="products__item item-product">
                    @if ($product['image'] != 'gift-card' && $product['discount'] != 0)
                        <span class="card__label">-{{ $product['discount'] }}%</span>
                    @endif
                    <a href="{{ route('home.product', $product['url']) }}">
                        <div class="item-product__info">
                            <div class="item-product__image">
                                @if ($product['image'] == 'gift-card')
                                    <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                                @else
                                    <picture>
                                        <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                        <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                                    </picture>
                                @endif
                            </div>
                            <div class="item-product__data">
                                <div class="item-product__name">{{ $product['name'] }}</div>
                                <div class="item-product__company">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </div>
                                <div class="item-product__bottom-row">
                                    <div class="item-product__price">{{ $Currency::convert($product['price'], false, true) }}</div>
                                    <a type="button" href="{{ route('home.product', $product['url']) }}" class="item-product__button button button--filled button--narrow">
                                        @if (!in_array(App::currentLocale(), ['de', 'it', 'gr', 'nl', 'hu', 'pt', 'es']))
                                            {{__('text.common_buy_button')}}
                                        @else
                                            <svg width="18.5" height="21.5">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                            </svg>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                        <p class="item-product__desrc">{{ $product['desc'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div> --}}
@else
    <div class="products">
        <h2 class="products__title title" id="scroll">{{__('text.search_result_title_page')}}  «{{$search_text}}»</h2>
        <div class="products__items">
            @foreach ($products as $product)
                <div class="products__item item-product">
                    @if ($product['image'] != 'gift-card' && $product['discount'] != 0)
                        <span class="card__label">-{{ $product['discount'] }}%</span>
                    @endif
                    <a href="{{ route('home.product', $product['url']) }}">
                        <div class="item-product__info">
                            <div class="item-product__image">
                                @if ($product['image'] == 'gift-card')
                                    <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                                @else
                                    <picture>
                                        <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                        <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                                    </picture>
                                @endif
                                {{-- <img loading="lazy" src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}"> --}}
                            </div>
                            <div class="item-product__data">
                                <div class="item-product__name">{{ $product['name'] }}</div>
                                <div class="item-product__company">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </div>
                                <div class="item-product__bottom-row">
                                    <div class="item-product__price">{{ $Currency::convert($product['price'], false, true) }}</div>
                                    <a type="button" href="{{ route('home.product', $product['url']) }}" class="item-product__button button button--filled button--narrow">
                                        @if (!in_array(App::currentLocale(), ['de', 'it', 'gr', 'nl', 'hu', 'pt', 'es']))
                                            {{__('text.common_buy_button')}}
                                        @else
                                            <svg width="18.5" height="21.5">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                            </svg>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                        <p class="item-product__desrc">{{ $product['desc'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif

@endsection
