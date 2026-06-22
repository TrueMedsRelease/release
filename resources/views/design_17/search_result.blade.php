@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-container">
    @if (count($products) == 0)
        <h2 class="no_product_head" style="margin-bottom: 20px">{{ __("text.common_product_text") }} «{{ $search_text }}» {{ __("text.search_not_found") }}</h2>
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

        {{-- <h2>{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h1> --}}
        <h2 style="margin-bottom: 20px;">{{__('text.search_result_best_for_search')}}</h2>
        <div class="product-cards">
            <div class="cards">
                <article class="card">
                    <div class="card__header">
                        <h2 class="card__title">
                            <a href="{{ route('home.bonus_referral_program') }}">{{ __('text.bonus_card_ref_programm') }}</a>
                        </h2>
                        <div class="card__ingredients">
                            <span class="card__ingredient">
                                {{ __('text.save_earn') }}
                            </span>
                        </div>
                    </div>
                    <div class="card__img">
                        <picture style="max-height: 126px; max-width: 126px;">
                            <img loading="lazy" src="{{ asset("$design/images/bonus_programm.png") }}" style="max-height: 126px; max-width: 126px;">
                        </picture>
                    </div>
                    <div class="card__footer">
                        <div class="card__price-wrapper">
                            <span class="card__price"></span>
                        </div>
                        <button class="card__button button button--outlined" onclick="location.href = '{{ route('home.bonus_referral_program') }}'">
                            {{ __('text.bonus_view_more') }}
                        </button>
                    </div>
                </article>
                @foreach ($bestsellers as $product)
                    <article class="card">
                        <div class="card__header">
                            <h2 class="card__title">
                                <a href="{{ route('home.product', $product['url']) }}">{{ $product['name'] }}</a>
                            </h2>
                            <div class="card__ingredients">
                                <span class="card__ingredient">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </span>
                            </div>
                        </div>
                        <div class="card__img">
                            @if ($product['id'] == 616)
                                <picture>
                                    <source type="image/webp" srcset="{{ asset("$design/img/products/gift-125w.webp") }} 1x, {{ asset("$design/img/products/gift-251w.webp 2x") }}">
                                    <img src="{{ asset("$design/img/products/gift-125w.jpg") }}" srcset="{{ asset("$design/img/products/gift-125w.jpg") }} 1x, {{ asset("$design/img/products/gift-251w.jpg 2x") }}"
                                    width="126" height="126" alt="{{ $product['image'] }}">
                                </picture>
                            @else
                                <picture style="max-height: 126px; max-width: 126px;">
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="max-height: 126px; max-width: 126px;">
                                </picture>
                            @endif
                        </div>
                        @if ($product['id'] != 616)
                            @if (!in_array($product['id'], [619, 620, 483, 484, 501, 615]))
                                <div class="card__variants">
                                    @foreach ($product['product_dosages'] as $dosage)
                                        <span class="card__variant">{{ $dosage }}</span>
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        <div class="card__footer">
                            <div class="card__price-wrapper">
                                @if ($product['id'] != 616)
                                    <span class="card__price">{{ $Currency::convert($product['price'], false, true) }} {{ strtolower(__("text.common_per_pill")) }}</span>
                                    {{-- <span class="card__discount">Retail Price €4.77</span> --}}
                                @else
                                    <span class="card__price">{{ $Currency::convert($product['price'], false, true) }}</span>
                                @endif
                            </div>
                            <button class="card__button button button--outlined" onclick="location.href = '{{ route('home.product', $product['url']) }}'">
                                {{ __('text.product_add_to_cart_text') }}
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @else
        <h1 class="h1">{{__('text.search_result_title_page')}} «{{ $search_text }}»</h1>
        <div class="product-cards">
            <div class="cards">
                <article class="card">
                    <div class="card__header">
                        <h2 class="card__title">
                            <a href="{{ route('home.bonus_referral_program') }}">{{ __('text.bonus_card_ref_programm') }}</a>
                        </h2>
                        <div class="card__ingredients">
                            <span class="card__ingredient">
                                {{ __('text.save_earn') }}
                            </span>
                        </div>
                    </div>
                    <div class="card__img">
                        <picture style="max-height: 126px; max-width: 126px;">
                            <img loading="lazy" src="{{ asset("$design/images/bonus_programm.png") }}" style="max-height: 126px; max-width: 126px;">
                        </picture>
                    </div>
                    <div class="card__footer">
                        <div class="card__price-wrapper">
                            <span class="card__price"></span>
                        </div>
                        <button class="card__button button button--outlined" onclick="location.href = '{{ route('home.bonus_referral_program') }}'">
                            {{ __('text.bonus_view_more') }}
                        </button>
                    </div>
                </article>
                @foreach ($products as $product)
                    <article class="card">
                        <div class="card__header">
                            <h2 class="card__title">
                                <a href="{{ route('home.product', $product['url']) }}">{{ $product['name'] }}</a>
                            </h2>
                            <div class="card__ingredients">
                                <span class="card__ingredient">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </span>
                            </div>
                        </div>
                        <div class="card__img">
                            @if ($product['id'] == 616)
                                <picture>
                                    <source type="image/webp" srcset="{{ asset("$design/img/products/gift-125w.webp") }} 1x, {{ asset("$design/img/products/gift-251w.webp 2x") }}">
                                    <img src="{{ asset("$design/img/products/gift-125w.jpg") }}" srcset="{{ asset("$design/img/products/gift-125w.jpg") }} 1x, {{ asset("$design/img/products/gift-251w.jpg 2x") }}"
                                    width="126" height="126" alt="{{ $product['image'] }}">
                                </picture>
                            @else
                                <picture style="max-height: 126px; max-width: 126px;">
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="max-height: 126px; max-width: 126px;">
                                </picture>
                            @endif
                        </div>
                        @if ($product['id'] != 616)
                            @if (!in_array($product['id'], [619, 620, 483, 484, 501, 615]))
                                <div class="card__variants">
                                    @foreach ($product['product_dosages'] as $dosage)
                                        <span class="card__variant">{{ $dosage }}</span>
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        <div class="card__footer">
                            <div class="card__price-wrapper">
                                @if ($product['id'] != 616)
                                    <span class="card__price">{{ $Currency::convert($product['price'], false, true) }} {{ strtolower(__("text.common_per_pill")) }}</span>
                                    {{-- <span class="card__discount">Retail Price €4.77</span> --}}
                                @else
                                    <span class="card__price">{{ $Currency::convert($product['price'], false, true) }}</span>
                                @endif
                            </div>
                            <button class="card__button button button--outlined" onclick="location.href = '{{ route('home.product', $product['url']) }}'">
                                {{ __('text.product_add_to_cart_text') }}
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection