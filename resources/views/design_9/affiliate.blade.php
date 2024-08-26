
@extends($design . '.layouts.main')

@section('title', __('text.affiliate_title'))

@section('content')
<div class="bonus_block all_padding">
    <div class="bonus1">
        <img src="{{ asset("$design/images/bonus1_1.png") }}">
    </div>
    <div class="bonus2">
        <img src="{{ asset("$design/images/bonus2_2.png") }}">
    </div>
</div>
<main class="default">
    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{__('text.affiliate_title')}}</h1>
                <div class="message_sended hidden">
                    <h2>{{__('text.affiliate_thanks')}}</h2>
                    <br>
                    <p>{{__('text.affiliate_sended')}}</p>
                </div>
                <form class="form" id = "message_send_form" method="post">
                    <div class="form__body">
                        <div class="form__inner">
                            <div class="form__default-rows">
                                <div class="form_rows_top">
                                    <div class="form__row">
                                        <label for="name" class="form__label">{{__('text.affiliate_name')}}</label>
                                        <input data-required id = "name" autocomplete="off" type="text" name="form[]" data-error="" placeholder="{{__('text.affiliate_name')}}" class="form__input input">
                                    </div>
                                    <div class="form__row">
                                        <label for="email" class="form__label">{{__('text.affiliate_email')}}</label>
                                        <input data-required id = "email" autocomplete="off" type="text" name="form[]" data-error="" placeholder="{{__('text.affiliate_email')}}" class="form__input input">
                                    </div>
                                    <div class="form__row">
                                        <label for="name" class="form__label">{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}</label>
                                        <input autocomplete="off" id = "jabber" type="text" name="form[]" data-error="" placeholder="{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}" class="form__input input">
                                    </div>
                                </div>
                                <div class="form__row form__row--top-alignment">
                                    <label for="name" class="form__label">{{__('text.affiliate_message')}}</label>
                                    <textarea autocomplete="off" id="message" type="text" name="form[]" data-error="" placeholder="{{__('text.affiliate_message')}}" class="form__input input"></textarea>
                                </div>
                            </div>
                            <div class="form__row form__row--captcha">
                                <label for="name" class="form__label">{{__('text.affiliate_code')}}</label>
                                <div class="form__input">
                                    <picture>
                                        <source srcset="/captcha" type="image/webp">
                                        <img src="/captcha">
                                    </picture>
                                    <input autocomplete="off" type="text" id = "captcha" name="form[captcha]" class="form__input input">
                                </div>
                            </div>
                            <button onclick="sendAjaxAffiliate()" type="button" class="form__button" id = "message_send_button">
                                <span>{{__('text.affiliate_send')}}</span>
                            </button>
                        </div>
                        <div class="form__desrc">
                            <div class="form__text-block">
                                <p>{{__('text.affiliate_contact_message')}}</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
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