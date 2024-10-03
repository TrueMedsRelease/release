@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="cart-page">
    <script>
        flagc = true;
    </script>
        <div class="cart-page__content basket" id="shopping_cart">

        </div>
        <aside class="cart-page__sidebar">
            <div class="cart-page__preference preference-page-cart">
                <div class="preference-page-cart__item">
                    <div class="preference-page-cart__top">
                        <div class="preference-page-cart__icon">
                            <picture><source srcset="{{ asset("$design/images/icons/c-02.webp") }}" type="image/webp"><img decoding="async" loading="lazy" src="{{ asset("$design/images/icons/c-02.png") }}" alt=""></picture>
                        </div>
                        <h2 class="preference-page-cart__label">{{__('text.cart_free_regular')}}</h2>
                    </div>
                    <p class="preference-page-cart__descr">{{__('text.cart_sum_regular')}}</p>
                </div>
                <div class="preference-page-cart__item">
                    <div class="preference-page-cart__top">
                        <div class="preference-page-cart__icon">
                            <picture><source srcset="{{ asset("$design/images/icons/c-03.webp") }}" type="image/webp"><img decoding="async" loading="lazy" src="{{ asset("$design/images/icons/c-03.png") }}" alt=""></picture>
                        </div>
                        <h2 class="preference-page-cart__label">{{__('text.cart_free_express')}}</h2>
                    </div>
                    <p class="preference-page-cart__descr">{{__('text.cart_sum_express')}}</p>
                </div>
                <div class="preference-page-cart__item">
                    <div class="preference-page-cart__top">
                        <div class="preference-page-cart__icon">
                            <picture><source srcset="{{ asset("$design/images/icons/c-04.webp") }}" type="image/webp"><img decoding="async" loading="lazy" src="{{ asset("$design/images/icons/c-04.png") }}" alt=""></picture>
                        </div>
                        <h2 class="preference-page-cart__label">{{__('text.cart_secret1')}} {{__('text.cart_secret2')}}</h2>
                    </div>
                    <p class="preference-page-cart__descr">{{__('text.cart_description_secret')}}</p>
                </div>
                <div class="preference-page-cart__item">
                    <div class="preference-page-cart__top">
                        <div class="preference-page-cart__icon">
                            <picture><source srcset="{{ asset("$design/images/icons/c-05.webp") }}" type="image/webp"><img decoding="async" loading="lazy" src="{{ asset("$design/images/icons/c-05.png") }}" alt=""></picture>
                        </div>
                        <h2 class="preference-page-cart__label">{{__('text.cart_moneyback1')}} {{__('text.cart_moneyback2')}}</h2>
                    </div>
                    <p class="preference-page-cart__descr">{{__('text.cart_description_moneyback')}}</p>
                </div>
            </div>
        </aside>
    </div>
@endsection
