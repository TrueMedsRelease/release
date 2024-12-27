@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="main">
    <!-- content -->
    @if (count($products) == 0)
        <h4 class="best-sellers__title title no_product_head" style="margin-bottom: 20px">{{ __("text.common_product_text") }} «{{ $search_text }}» {{ __("text.search_not_found") }}</h2>
        <div class="no_product_text" style="margin-bottom: 10px">{{ __("text.search_not_carry") }} «{{ $search_text }}» {{ __("text.search_this_time") }}</div>
        <div class="no_product_text" style="margin-bottom: 20px">{{ __("text.search_product_request") }}</div>
        <div class="button" id="go_to_contact_us" style="width: 100%; max-width: 300px; min-width: 270px; margin-bottom: 20px" onclick="location.href = '{{ route('home.contact_us') }}'">
            {{ __("text.common_contact_us_main_menu_item") }}
        </div>
        <section class="page__best-sellers best-sellers" >
            {{-- <h4 class="best-sellers__title title" id = "scroll">{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h4> --}}
            <h2 class="best-sellers__title title">{{__('text.search_result_best_for_search')}}</h2>
            <div class="best-sellers__items">
            @foreach ($bestsellers as $product)
                <a href="{{ route('home.product', $product['url']) }}" class="product-card">
                    <div class="product-card__body">
                        <div class="product-card__top">
                            <div class="product-card__info">
                                <h3 class="product-card__name">{{ $product['name'] }}</h3>
                                <h4 class="product-card__company">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </h4>
                            </div>
                            <div class="product-card__price">{{ $Currency::convert($product['price'], false, true) }}</div>
                        </div>
                        @if ($product['image'] != 'gift-card' && $product['discount'] != 0)
                            <span class="card__label">-{{ $product['discount'] }}%</span>
                        @endif
                        <div class="product-card__image">
                            @if ($product['image'] == 'gift-card')
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                                </picture>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="product-card__button button button--primary">
                        <picture><source srcset="{{ asset("$design/images/icons/cart.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/icons/cart.png") }}" width="23" height="23" alt=""></picture>
                    </button>
                </a>
            @endforeach
            </div>
        </section>
    @else
        <section class="page__best-sellers best-sellers">
        <h2 class="best-sellers__title title" id = "scroll">{{__('text.search_result_title_page')}} «{{ $search_text }}»</h2>
        <div class="best-sellers__items">
            @foreach ($products as $product)
                <a href="{{ route('home.product', $product['url']) }}" class="product-card">
                    <div class="product-card__body">
                        <div class="product-card__top">
                            <div class="product-card__info">
                                <h3 class="product-card__name">{{ $product['name'] }}</h3>
                                <h4 class="product-card__company">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </h4>
                            </div>
                            <div class="product-card__price">{{ $Currency::convert($product['price'], false, true) }}</div>
                        </div>
                        @if ($product['image'] != 'gift-card' && $product['discount'] != 0)
                            <span class="card__label">-{{ $product['discount'] }}%</span>
                        @endif
                        <div class="product-card__image">
                            @if ($product['image'] == 'gift-card')
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                                </picture>
                            @endif
                            {{-- <img loading="lazy" src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" width="140" height="140" alt="{{ $product['name'] }}"> --}}
                        </div>
                    </div>
                    <button type="button" class="product-card__button button button--primary">
                        <picture><source srcset="{{ asset("$design/images/icons/cart.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/icons/cart.png") }}" width="23" height="23" alt=""></picture>
                    </button>
                </a>
            @endforeach
        </div>
        </section>
    @endif
    <!-- /.best-sellers -->
    <!-- END content -->
    </div>
    </div>
    </div>
    </div>

    </main>

@endsection
