@extends($design . '.layouts.main')

@section('title', 'TrueMeds')
@section('title_2', __('text.main_best_selling_title'))

@section('content')
<main class="page">
    <section class="page__hero hero">
        <h1 class="visually-hidden">Some title for seo</h1>
        <div class="hero__container">
            <div class="hero__body">
                <div class="hero__content">
                    <div class="hero__top">
                        <div class="hero__digits">1 000 000</div>
                        <div class="hero__info">
                            <div class="hero__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="98" height="18" alt="">
                            </div>
                            <p class="hero__text">{{__('text.common_customers')}}</p>
                        </div>
                    </div>
                    <div class="hero__items">
                        <div class="hero__item item-hero">
                            <div class="item-hero__icon">
                                <img src="{{ asset("$design/images/icons/pref-01.svg") }}" alt="">
                            </div>
                            <div class="item-hero__info">
                                <h2 class="item-hero__label">{{__('text.common_save')}}</h2>
                                <p class="item-hero__text">{{__('text.common_discount')}}</p>
                            </div>
                        </div>
                        <div class="hero__item item-hero">
                            <div class="item-hero__icon">
                                <img src="{{ asset("$design/images/icons/pref-02.svg") }}" alt="">
                            </div>
                            <div class="item-hero__info">
                                <h2 class="item-hero__label">{{__('text.common_delivery')}}</h2>
                                <p class="item-hero__text">{{__('text.common_receive')}}</p>
                            </div>
                        </div>
                        <div class="hero__item item-hero">
                            <div class="item-hero__icon">
                                <img src="{{ asset("$design/images/icons/pref-03.svg") }}" alt="">
                            </div>
                            <div class="item-hero__info">
                                <h2 class="item-hero__label">{{__('text.common_prescription')}}</h2>
                                <p class="item-hero__text">{{__('text.common_restrictions')}}</p>
                            </div>
                        </div>
                        <div class="hero__item item-hero">
                            <div class="item-hero__icon">
                                <img src="{{ asset("$design/images/icons/pref-04.svg") }}" alt="">
                            </div>
                            <div class="item-hero__info">
                                <h2 class="item-hero__label">{{__('text.common_moneyback')}}</h2>
                                <p class="item-hero__text">{{__('text.common_refund')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hero__image">
                    <picture><source srcset="{{ asset("$design/images/hero/01.webp") }}" type="image/webp"><img src="{{ asset("$design/images/hero/01.png") }}" alt=""></picture>
                </div>
                <div class="hero__bg">
                    <picture>
                        <source srcset="{{ asset("$design/images/hero/bg-xs.png") }}" media="(max-width: 479px)" type="image/webp">
                        <source srcset="{{ asset("$design/images/hero/bg.png") }}" type="image/webp">
                        <img src="{{ asset("$design/images/hero/hero.jpg") }}" alt="">
                    </picture>
                </div>
            </div>
        </div>
    </section>

    <section class="page__verifed verifed">
        <div class="verifed__container">
            <h2 class="visually-hidden">Our parners</h2>
            <div class="verifed__body">
                <div class="verifed__about">
                    <img src="{{ asset("$design/images/icons/verified.svg") }}" alt="">
                </div>
                <div class="verifed__items">
                    <div class="verifed__item">
                        <img src="{{ asset("$design/images/partners/fda.svg") }}" alt="">
                    </div>
                    <div class="verifed__item">
                        <picture><source srcset="{{ asset("$design/images/partners/pgeu.webp") }}" type="image/webp"><img src="{{ asset("$design/images/partners/pgeu.png") }}" alt=""></picture>
                    </div>
                    <div class="verifed__item">
                        <picture><source srcset="{{ asset("$design/images/partners/cipa.webp") }}" type="image/webp"><img src="{{ asset("$design/images/partners/cipa.png") }}" alt=""></picture>
                    </div>
                    <div class="verifed__item">
                        <img src="{{ asset("$design/images/partners/mastercard.svg") }}" alt="">
                    </div>
                    <div class="verifed__item">
                        <img src="{{ asset("$design/images/partners/visa.svg") }}" alt="">
                    </div>
                    <div class="verifed__item">
                        <img src="{{ asset("$design/images/partners/mcafee.svg") }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- <section class="pay-index">
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
    </section> --}}

    <section class="page__bestsellers bestsellers">
        <aside class="categories-sidebar">
            <div class="categories-sidebar__inner">
                <div data-spollers class="categories-sidebar__spollers spollers">
                    <div class="spollers__item">
                        <button type="button" data-spoller class="spollers__title _spoller-active">{{__('text.main_best_selling_title')}}</button>
                        <ul class="spollers__body main_bestsellers" id="main_bestsellers_body">
                            @foreach ($bestsellers as $bestseller)
                                <li class="spollers__item-list"><a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span style="font-size: 12px;">${{ $bestseller['price'] }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    @foreach ($menu as $category)
                        <div class="spollers__item">
                            <button type="button" data-spoller class="spollers__title _spoller-active">{{ $category['name'] }}</button>
                            <ul class="spollers__body" id="this_product_category">
                                @foreach ($category['products'] as $item)
                                    <li class="spollers__item-list">
                                        <a href="{{ route('home.product', $item['url']) }}">
                                            {{ $item['name'] }}
                                        </a>
                                        <span style="font-size: 12px;">${{ $item['price'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
        <div class="bestsellers__container">
            <h2 class="bestsellers__title title">{{__('text.main_best_selling_title')}}</h2>
            <div class="bestsellers__body">
                @foreach ($bestsellers as $product)
                    @if ($loop->iteration == 2)
                        <div class="product-card product-card--offers">
                            <picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/01.jpg") }}" alt=""></picture>
                        </div>

                        <div class="product-card product-card--offers">
                            <picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/02.jpg") }}" alt=""></picture>
                        </div>
                    @endif
                    <div class="product-card">
                        <a href="{{ route('home.product', $product['url']) }}" class="product-card__image">
                            <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" width="140" height="140" alt="{{ $product['name'] }}">
                        </a>
                        <a href="{{ route('home.product', $product['url']) }}" class="product-card__info">
                            <h3 class="product-card__label">{{ $product['name'] }}</h3>
                            <h4 class="product-card__company">
                                @foreach ($product['aktiv'] as $aktiv)
                                    {{ $aktiv }}
                                @endforeach
                            </h4>
                        </a>
                        <div class="product-card__bottom">
                            <div class="product-card__left">
                                <div class="product-card__price">${{ $product['price'] }}</div>
                            </div>
                            <button type="button" class="product-card__button button button--accent" title="{{__('text.product_add_to_cart_text')}}" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                                <svg width="24" height="24">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                </svg>
                                <span>{{__('text.product_add_to_cart_text_d2')}}</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</main>

@endsection
