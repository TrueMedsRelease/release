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
@endif

</div>

@endsection
