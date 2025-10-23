@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<section class="pay-index">
            <div class="pay-index__container">
                <ul class="pay-index__list">
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#visa') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#mastercard') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#maestro') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#discover') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amex') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#jsb') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' union-pay' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#unionpay') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#dinners-club') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#apple-pay') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#google-pay') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amazon-pay') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#stripe') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#paypal') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#sepa') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#cashapp') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#adyen') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#skrill') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#worldpay') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#payline') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#bitcoin') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#binance-coin') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#ethereum') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#litecoin') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#tron') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(erc20)') }}">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(trc20)') }}">
                        </svg>
                    </li>
                </ul>
            </div>
        </section>

<div class="hero-header__search">
    <div class="search-bar" data-dev>
        <form class="search-bar__input search-form" style="position: relative; display: flex" action="{{ route('search.search_product') }}" method = "POST">
            @csrf
            <button type="submit" class="search-bar__icon">
                <svg width="15" height="15">
                    <use xlink:href="{{ asset("$design/images/icon/icons.svg#svg-search") }}"></use>
                </svg>
                <span class="sr-only" style="display: none;">search</span>
            </button>
            <input id="autocomplete" autocomplete="off" type="text" name="search_text" data-error="Error" class="input" placeholder="{{__('text.common_search')}}">
            <ul class="search-bar__results" style="display: none;"></ul>
        </form>
        <div class="search-bar__nav" data-simplebar data-simplebar-auto-hide="false">
            <ul class="search-bar__letter-list">
                @foreach ($first_letters as $key => $active_letter)
                    <li class="search-bar__item-list">
                        @if ($active_letter)
                            <a href="{{ route('home.first_letter', $key) }}">{{ $key }}</a>
                        @else
                            {{ $key }}
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="checkup" onclick="location.href='{{ route('home.checkup') }}'">
    <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_big.png") }}">
</div>

<div class="sale-banners">
    <div class="happy-sale item">
        <span class="img">
            <img loading="lazy" src="{{ asset("$design/images/icon/ico-banner-01.svg") }}" alt="">
        </span>
        <span class="info">
            <span class="title">{{__('text.common_banner1_text1')}} <br>{{__('text.common_banner1_text2')}}</span>
            <span class="text">{{__('text.common_banner1_text3')}} <br> {{__('text.common_banner1_text4')}}</span>
        </span>
    </div>
    <div class="wow-sale item">
        <span class="img">
            <img loading="lazy" src="{{ asset("$design/images/icon/ico-banner-02.svg") }}" alt="">
        </span>
        <span class="info">
            <span class="title">{{__('text.common_banner2_text1')}} <br> {!!__('text.common_banner2_text2')!!}</span>
            <span class="text">{{__('text.common_banner2_text3')}} <br> {{__('text.common_banner2_text4')}}</span>
        </span>
    </div>
</div>

<div class="main_block">
    <div class="categories">
        <aside class="categories-sidebar">
            <div class="categories-sidebar__inner">
                <div data-spollers class="categories-sidebar__spollers spollers">
                    <div class="spollers__item">
                        <button type="button" data-spoller class="spollers__title _spoller-active">{{__('text.common_best_selling_title')}}</button>
                        <ul class="spollers__body main_bestsellers" id="main_bestsellers_body">
                            @foreach ($bestsellers as $bestseller)
                                <li class="spollers__item-list"><a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span style="font-size: 12px;">{{ $Currency::convert($bestseller['price'], false, true) }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    @foreach ($menu as $category)
                        <div class="spollers__item">
                            <button type="button" data-spoller class="spollers__title @if ($cur_category == $category['name']) _spoller-active @endif">{{ $category['name'] }}</button>
                            <ul class="spollers__body">
                                @foreach ($category['products'] as $item)
                                    <li class="spollers__item-list">
                                        <a href="{{ route('home.product', $item['url']) }}">
                                            {{ $item['name'] }}
                                        </a>
                                        <span style="font-size: 12px;">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
    @if (count($products) == 0)
        {{-- <div class="products">
            <h2 class="title-page no_product_head" style="margin-bottom: 20px">{{ __("text.common_product_text") }} «{{ $search_text }}» {{ __("text.search_not_found") }}</h2>
            <div class="no_product_text" style="margin-bottom: 10px">{{ __("text.search_not_carry") }} «{{ $search_text }}» {{ __("text.search_this_time") }}</div>
            <div class="no_product_text" style="margin-bottom: 20px">{{ __("text.search_product_request") }}</div>
            <div class="button" id="go_to_contact_us" onclick="location.href = '{{ route('home.contact_us') }}'">
                {{ __("text.common_contact_us_main_menu_item") }}
            </div>
        </div> --}}
        <div class="products">
            <h2 class="title-page no_product_head" style="margin-bottom: 20px">{{ __("text.common_product_text") }} «{{ $search_text }}» {{ __("text.search_not_found") }}</h2>
            <div class="no_product_text" style="margin-bottom: 10px">{{ __("text.search_not_carry") }} «{{ $search_text }}» {{ __("text.search_this_time") }}</div>
            <div class="no_product_text" style="margin-bottom: 20px">{{ __("text.search_product_request") }}</div>

            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                @php
                    $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
                @endphp
                <div class="button" id="go_to_contact_us" onclick="location.href = '{{ route('home.contact_us', '_' . $domainWithoutZone) }}'">
                    {{ __("text.common_contact_us_main_menu_item") }}
                </div>
            @else
                <div class="button" id="go_to_contact_us" onclick="location.href = '{{ route('home.contact_us', '') }}'">
                    {{ __("text.common_contact_us_main_menu_item") }}
                </div>
            @endif


            {{-- <h2 class="title-page">{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h2> --}}
            <h2 class="title-page">{{__('text.search_result_best_for_search')}}</h2>
            <div class="product-list">
                @foreach ($bestsellers as $product)
                    <div class="item">
                        @if ($product['id'] != 616 && $product['discount'] != 0)
                            <span class="card__label">-{{ $product['discount'] }}%</span>
                        @endif
                        <a href="{{ route('home.product', $product['url']) }}" class="img">
                            @if ($product['id'] == 616)
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                                </picture>
                            @endif
                        </a>
                        <div class="info">
                            <div class="box">
                                <a href="{{ route('home.product', $product['url']) }}" class="name">{{ $product['name'] }}</a>
                                <a href="{{ route('home.product', $product['url']) }}" class="cat">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </a>
                            </div>
                            <div class="box">
                                <span class="price">{{ $Currency::convert($product['price'], false, true) }}</span>
                                <a href="{{ route('home.product', $product['url']) }}" class="btn btn-primary main">
                                    <img loading="lazy" src="{{ asset("$design/images/icon/ico-basket.svg") }}" alt="">
                                    <span>{{__('text.common_add_to_cart_text_d2')}}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="products">
            <h2 class="title-page">{{__('text.search_result_title_page')}}  «{{$search_text}}»</h2>
            <div class="product-list">
                @foreach ($products as $product)
                    <div class="item">
                        @if ($product['id'] != 616 && $product['discount'] != 0)
                            <span class="card__label">-{{ $product['discount'] }}%</span>
                        @endif
                        <a href="{{ route('home.product', $product['url']) }}" class="img">
                            @if ($product['id'] == 616)
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                                </picture>
                            @endif
                        </a>
                        <div class="info">
                            <div class="box">
                                <a href="{{ route('home.product', $product['url']) }}" class="name">{{ $product['name'] }}</a>
                                <a href="{{ route('home.product', $product['url']) }}" class="cat">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv['name'] }}
                                    @endforeach
                                </a>
                            </div>
                            <div class="box">
                                <span class="price">{{ $Currency::convert($product['price'], false, true) }}</span>
                                <a href="{{ route('home.product', $product['url']) }}" class="btn btn-primary main">
                                    <img loading="lazy" src="{{ asset("$design/images/icon/ico-basket.svg") }}" alt="">
                                    <span>{{__('text.common_add_to_cart_text_d2')}}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@endsection
