@extends($design . '.layouts.main')

@section('title', __('text.cart_cart_title'))

@section('content')
<div class="bonus_block all_padding">
    <div class="bonus1">
        <img src="{{ asset("$design/images/bonus1_1.png") }}">
    </div>
    <div class="bonus2">
        <img src="{{ asset("$design/images/bonus2_2.png") }}">
    </div>
</div>

<main class="basket" id="shopping_cart">

</main>

<div class="subscribe_body">
    <div class="left_block">
        <div class="subscribe_img">
            <img src="{{ asset("$design/images/icons/subscribe.svg") }}">
        </div>
        <div class="text_subscribe">
            <span class="top_text">{{__('text.common_subscribe')}}</span>
            <span class="bottom_text">{{__('text.common_spec_offer')}}</span>
        </div>
    </div>
    <div class="right_block">
        <input type="text" placeholder="Email" class="form__input input" id="email_sub">
        <div class="button_sub">
            <img src="{{ asset("$design/images/icons/subscribe_mini.svg") }}" class="sub_mini">
            <span class="button_text">{{__('text.common_subscribe')}}</span>
        </div>
    </div>
</div>

<section class="ship-index">
    <div class="ship-index__container">
        <ul class="ship-index__list">
            <li class="ship-index__item">
                <img src="/images/shipping/usps.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/ems.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/dhl.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/ups.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/fedex.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/tnt.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/postnl.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/deutsche_post.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/dpd.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/gls.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/australia_post.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/colissimo.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/correos.svg" alt="">
            </li>
        </ul>
    </div>
</section>

<div class="reviews_block">
    <div class="review">
        <div class="review_top">
            <div class="person_name">{!!__('text.testimonials_author_t_1')!!}</div>
            <div class="stars">
                <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
            </div>
        </div>
        <div class="review_text">{{__('text.testimonials_t_1')}}</div>
    </div>
    <div class="review">
        <div class="review_top">
            <div class="person_name">{!!__('text.testimonials_author_t_7')!!}</div>
            <div class="stars">
                <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
            </div>
        </div>
        <div class="review_text">{{__('text.testimonials_t_7')}}</div>
    </div>
    <div class="review">
        <div class="review_top">
            <div class="person_name">{!!__('text.testimonials_author_t_13')!!}</div>
            <div class="stars">
                <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
            </div>
        </div>
        <div class="review_text">{{__('text.testimonials_t_13')}}</div>
    </div>
    <div class="review">
        <div class="review_top">
            <div class="person_name">{!!__('text.testimonials_author_t_17')!!}</div>
            <div class="stars">
                <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
            </div>
        </div>
        <div class="review_text">{{__('text.testimonials_t_17')}}</div>
    </div>
</div>

@endsection
