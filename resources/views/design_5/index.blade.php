@extends($design . '.layouts.main')

@section('title', __('text.common_best_selling_title'))

@section('content')
<script>
    flagm = true;
</script>

<div class="banner-box">
    <div class="banner">
        <div class="info">
            <h2>1 000 000</h2>
            <div class="stars">
                <span></span><span></span><span></span><span></span><span></span>
            </div>
            <div class="text">
                <p>{{__('text.common_customers')}}</p>
            </div>
        </div>
    </div>
    <div class="information-banner">
        <div class="information">
            <div class="item">
                <strong class="name">
                    <img src="{{ asset("$design/images/icon/ico-info-01.svg") }}" alt="">
                    <span>{{__('text.common_save')}}</span>
                </strong>
                <p>{{__('text.common_discount')}}</p>
            </div>
            <div class="item">
                <strong class="name">
                    <img src="{{ asset("$design/images/icon/ico-info-02.svg") }}" alt="">
                    <span>{{__('text.common_delivery')}}</span>
                </strong>
                <p>{{__('text.common_receive')}}</p>
            </div>
            <div class="item">
                <strong class="name">
                    <img src="{{ asset("$design/images/icon/ico-info-03.svg") }}" alt="">
                    <span>{{__('text.common_prescription')}}</span>
                </strong>
                <p>{{__('text.common_restrictions')}}</p>
            </div>
            <div class="item">
                <strong class="name">
                    <img src="{{ asset("$design/images/icon/ico-info-04.svg") }}" alt="">
                    <span>{{__('text.common_moneyback')}}</span>
                </strong>
                <p>{{__('text.common_refund')}}</p>
            </div>
        </div>
    </div>
    <div class="information-banner sales-info-banner">
        <div class="sales-info">
            <a href="{{ config('app.url') }}/trial-ed-pack" class="item">
                  <span class="img">
                    <span class="price">$4</span>
                    <img src="{{ asset("$design/images/product/img-01.png") }}" alt="">
                  </span>
                <strong class="name">Trial ED Pack</strong>
            </a>
            <a href="{{ config('app.url') }}/super-ed-pack" class="item">
                  <span class="img">
                    <span class="price">$3</span>
                    <img src="{{ asset("$design/images/product/img-01.png") }}" alt="">
                  </span>
                <strong class="name">Super ED Pack</strong>
            </a>
        </div>
    </div>
</div>

<section class="pay-index">
    <div class="pay-index__container">
        <ul class="pay-index__list">
            <li class="pay-index__item">
                <img src="/images/pay_icons/visa.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/mastercard.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/maestro.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/discover.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/amex.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/jsb.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/unionpay.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/dinners-club.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/apple-pay.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/google-pay.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/amazon-pay.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/stripe.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/paypal.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/sepa.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/cashapp.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/adyen.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/skrill.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/worldpay.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/payline.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/bitcoin.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/binance-coin.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/ethereum.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/litecoin.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/tron.svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/usdt(erc20).svg" alt="">
            </li>
            <li class="pay-index__item">
                <img src="/images/pay_icons/usdt(trc20).svg" alt="">
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
                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-search") }}"></use>
                </svg>
                <span class="sr-only" style="display: none;">search</span>
            </button>
            <input id="autocomplete" autocomplete="off" type="text" name="search_text" data-error="Error" class="input" placeholder="{{__('text.common_search')}}">
            <ul class="search-bar__results" style="display: none;"></ul>
        </form>
        <div class="search-bar__nav" data-simplebar data-simplebar-auto-hide="false">
            <ul class="search-bar__letter-list">
                @foreach (range('A', 'Z') as $l)
                    <li class="search-bar__item-list">
                        <a href="{{ route('home.first_letter', $l) }}">{{ $l }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="sale-banners">
    <div class="happy-sale item">
        <span class="img">
            <img src="{{ asset("$design/images/icon/ico-banner-01.svg") }}" alt="">
        </span>
        <span class="info">
            <span class="title">{{__('text.common_banner1_text1')}} <br>{{__('text.common_banner1_text2')}}</span>
            <span class="text">{{__('text.common_banner1_text3')}} <br> {{__('text.common_banner1_text4')}}</span>
        </span>
    </div>
    <div class="wow-sale item">
        <span class="img">
            <img src="{{ asset("$design/images/icon/ico-banner-02.svg") }}" alt="">
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
                        {{-- {if $cur_category.name eq $data.product_info.category_name}
                            <div class="spollers__item">
                                <button type="button" data-spoller class="spollers__title _spoller-active">{$cur_category.name}</button>
                                <ul class="spollers__body" id="this_product_category">
                                    {foreach item=cur_product from=$cur_category.products}
                                        <li class="spollers__item-list">
                                            <a href="{$path.page}/{$cur_product.url}">
                                                {$cur_product.name}
                                            </a>
                                            <span style="font-size: 12px;">{$cur_product.min_price_per_pill}</span>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                        {else} --}}
                            <div class="spollers__item">
                                <button type="button" data-spoller class="spollers__title">{{ $category['name'] }}</button>
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
                        {{-- {/if} --}}
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
    <div class="products">
        <div class="product-list">
            @foreach ($bestsellers as $product)
                <div class="item">
                    <a href="{{ route('home.product', $product['url']) }}" class="img">
                        <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" width="140" height="140" alt="{{ $product['name'] }}">
                    </a>
                    <div class="info">
                        <div class="box">
                            <a href="{{ route('home.product', $product['url']) }}" class="name">{{ $product['name'] }}</a>
                            <a href="{{ route('home.product', $product['url']) }}" class="cat">
                                @foreach ($product['aktiv'] as $aktiv)
                                    {{ $aktiv }}
                                @endforeach
                            </a>
                        </div>
                        <div class="box">
                            <span class="price">{{ $Currency::convert($product['price'], false, true) }}</span>
                            <a href="{{ route('home.product', $product['url']) }}" class="btn btn-primary main">
                                <img src="{{ asset("$design/images/icon/ico-basket.svg") }}" alt="">
                                <span>{{__('text.common_add_to_cart_text_d2')}}</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection