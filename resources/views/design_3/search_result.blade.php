@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@if (count($products) == 0)
    {{-- @section('title_3')
        <h4 class="page__title title">{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h4>
        <h2 class="page__title title">{{__('text.search_result_best_for_search')}}</h2>
    @endsection --}}
@else
    @section('title_2', __('text.search_result_title_page') . ' «' . $search_text . '»')
@endif

@section('content')
@if (count($products) == 0)
        {{-- <h2 class="page__title title no_product_head" style="margin-bottom: 20px">{{ __("text.common_product_text") }} «{{ $search_text }}» {{ __("text.search_not_found") }}</h2>
        <div class="no_product_text" style="margin-bottom: 10px; font-size: 16px;">{{ __("text.search_not_carry") }} «{{ $search_text }}» {{ __("text.search_this_time") }}</div>
        <div class="no_product_text" style="margin-bottom: 20px; font-size: 16px;">{{ __("text.search_product_request") }}</div>
        <div class="button" id="go_to_contact_us" onclick="location.href = '{{ route('home.contact_us') }}'">
            {{ __("text.common_contact_us_main_menu_item") }}
        </div> --}}
    <div class="page__products products">
        <h2 class="page__title title no_product_head" style="margin-bottom: 20px">{{ __("text.common_product_text") }} «{{ $search_text }}» {{ __("text.search_not_found") }}</h2>
        <div class="no_product_text" style="margin-bottom: 10px; font-size: 16px;">{{ __("text.search_not_carry") }} «{{ $search_text }}» {{ __("text.search_this_time") }}</div>
        <div class="no_product_text" style="margin-bottom: 20px; font-size: 16px;">{{ __("text.search_product_request") }}</div>

        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
            @php
                $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
            @endphp
            <div class="button" id="go_to_contact_us" style="margin-bottom: 20px" onclick="location.href = '{{ route('home.contact_us', '_' . $domainWithoutZone) }}'">
                {{ __("text.common_contact_us_main_menu_item") }}
            </div>
        @else
            <div class="button" id="go_to_contact_us" style="margin-bottom: 20px" onclick="location.href = '{{ route('home.contact_us', '') }}'">
                {{ __("text.common_contact_us_main_menu_item") }}
            </div>
        @endif

        <h2 class="page__title title">{{__('text.search_result_best_for_search')}}</h2>
        <div class="products__items">
            @foreach ($bestsellers as $product)
                <a href="{{ route('home.product', $product['url']) }}" class="item-product">
                    <div class="item-product__content">
                        <div class="item-product__top">
                            <div class="item-product__left">
                                <div class="item-product__name">{{ $product['name'] }}</div>
                                <p class="item-product__company">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </p>
                            </div>
                            <div class="item-product__price">{{ $Currency::convert($product['price'], false, true) }}</div>
                        </div>
                        @if ($product['id'] != 616 && $product['discount'] != 0)
                            <span class="card__label">-{{ $product['discount'] }}%</span>
                        @endif
                        <div class="item-product__image-ibg">
                            @if ($product['id'] == 616)
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                                </picture>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="item-product__button" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                        <svg width="24" height="20">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                        </svg>
                        <span>{{__('text.product_add_to_cart_text')}}</span>
                    </button>
                </a>
            @endforeach
        </div>
    </div>
    </div>
@else
    <div class="page__products products">
        <div class="products__items">
            @foreach ($products as $product)
                <a href="{{ route('home.product', $product['url']) }}" class="item-product">
                    <div class="item-product__content">
                        <div class="item-product__top">
                            <div class="item-product__left">
                                <div class="item-product__name">{{ $product['name'] }}</div>
                                <p class="item-product__company">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </p>
                            </div>
                            <div class="item-product__price">{{ $Currency::convert($product['price'], false, true) }}</div>
                        </div>
                        @if ($product['id'] != 616 && $product['discount'] != 0)
                            <span class="card__label">-{{ $product['discount'] }}%</span>
                        @endif
                        <div class="item-product__image-ibg">
                            @if ($product['id'] == 616)
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                                </picture>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="item-product__button" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                        <svg width="24" height="20">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                        </svg>
                        <span>{{__('text.product_add_to_cart_text')}}</span>
                    </button>
                </a>
            @endforeach
        </div>
    </div>
    </div>
@endif

</div>

@endsection
