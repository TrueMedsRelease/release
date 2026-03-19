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
    {{-- <div class="products">
        <h2 class="products__title title no_product_head" style="margin-bottom: 20px">{{ __("text.common_product_text") }} «{{ $search_text }}» {{ __("text.search_not_found") }}</h2>
        <div class="no_product_text" style="margin-bottom: 10px">{{ __("text.search_not_carry") }} «{{ $search_text }}» {{ __("text.search_this_time") }}</div>
        <div class="no_product_text" style="margin-bottom: 20px">{{ __("text.search_product_request") }}</div>
        <div class="button" id="go_to_contact_us" onclick="location.href = '{{ route('home.contact_us') }}'">
            {{ __("text.common_contact_us_main_menu_item") }}
        </div>
    </div> --}}
    <div class="products">
        <h2 class="products__title title no_product_head" style="margin-bottom: 20px">{{ __("text.common_product_text") }} «{{ $search_text }}» {{ __("text.search_not_found") }}</h2>
        <div class="no_product_text" style="margin-bottom: 10px">{{ __("text.search_not_carry") }} «{{ $search_text }}» {{ __("text.search_this_time") }}</div>
        <div class="no_product_text" style="margin-bottom: 20px">{{ __("text.search_product_request") }}</div>

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

        {{-- <h2 class="products__title title">{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h2> --}}
        <h2 class="products__title title">{{__('text.search_result_best_for_search')}}</h2>
        <div class="products__items">
            @foreach ($bestsellers as $product)
                <div class="products__item item-product">
                    @if ($product['id'] != 616 && $product['discount'] != 0)
                        <span class="card__label">-{{ $product['discount'] }}%</span>
                    @endif
                    <a href="{{ route('home.product', $product['url']) }}">
                        <div class="item-product__info">
                            <div class="item-product__image">
                                @if ($product['id'] == 616)
                                    <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                                @else
                                    <picture>
                                        <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                        <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
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
                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                <svg width="18.5" height="21.5">
                                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19 22" fill="currentColor" width="18.5" height="21.5">
                                                    <path fill-rule="evenodd" d="M0 7.715c0-.967.784-1.75 1.75-1.75h15.053c.966 0 1.75.783 1.75 1.75v6.017a7.768 7.768 0 0 1-7.768 7.768H7.768A7.768 7.768 0 0 1 0 13.732V7.715Zm1.75-.25a.25.25 0 0 0-.25.25v6.017A6.268 6.268 0 0 0 7.768 20h3.017a6.268 6.268 0 0 0 6.268-6.268V7.715a.25.25 0 0 0-.25-.25H1.75Z" clip-rule="evenodd"/>
                                                    <path fill-rule="evenodd" d="M6.757 2.166c-.627.413-.994.985-.994 1.742v7.018a.75.75 0 1 1-1.5 0V3.908c0-1.348.698-2.355 1.67-2.995C6.881.288 8.1 0 9.275 0c1.175 0 2.394.288 3.343.913.972.64 1.67 1.647 1.67 2.995v7.018a.75.75 0 1 1-1.5 0V3.908c0-.758-.367-1.33-.995-1.742-.649-.428-1.562-.666-2.518-.666-.956 0-1.87.238-2.519.666Z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
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
@else
    <div class="products">
        <h2 class="products__title title" id="scroll">{{__('text.search_result_title_page')}}  «{{$search_text}}»</h2>
        <div class="products__items">
            @foreach ($products as $product)
                <div class="products__item item-product">
                    @if ($product['id'] != 616 && $product['discount'] != 0)
                        <span class="card__label">-{{ $product['discount'] }}%</span>
                    @endif
                    <a href="{{ route('home.product', $product['url']) }}">
                        <div class="item-product__info">
                            <div class="item-product__image">
                                @if ($product['id'] == 616)
                                    <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                                @else
                                    <picture>
                                        <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                        <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                                    </picture>
                                @endif
                                {{-- <img loading="lazy" src="{{ $product['id'] != 616 ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}"> --}}
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
                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                <svg width="18.5" height="21.5">
                                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19 22" fill="currentColor" width="18.5" height="21.5">
                                                    <path fill-rule="evenodd" d="M0 7.715c0-.967.784-1.75 1.75-1.75h15.053c.966 0 1.75.783 1.75 1.75v6.017a7.768 7.768 0 0 1-7.768 7.768H7.768A7.768 7.768 0 0 1 0 13.732V7.715Zm1.75-.25a.25.25 0 0 0-.25.25v6.017A6.268 6.268 0 0 0 7.768 20h3.017a6.268 6.268 0 0 0 6.268-6.268V7.715a.25.25 0 0 0-.25-.25H1.75Z" clip-rule="evenodd"/>
                                                    <path fill-rule="evenodd" d="M6.757 2.166c-.627.413-.994.985-.994 1.742v7.018a.75.75 0 1 1-1.5 0V3.908c0-1.348.698-2.355 1.67-2.995C6.881.288 8.1 0 9.275 0c1.175 0 2.394.288 3.343.913.972.64 1.67 1.647 1.67 2.995v7.018a.75.75 0 1 1-1.5 0V3.908c0-.758-.367-1.33-.995-1.742-.649-.428-1.562-.666-2.518-.666-.956 0-1.87.238-2.519.666Z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
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
