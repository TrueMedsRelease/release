
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="bonus_block all_padding">
    <div class="bonus1">
        <img loading="lazy" src="{{ asset("$design/images/bonus1_1.png") }}">
    </div>
    <div class="bonus2">
        <img loading="lazy" src="{{ asset("$design/images/bonus2_2.png") }}">
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
                                        <img loading="lazy" id="captcha_image" src="{{ captcha_src() }}" style="border-radius: 14px;">
                                    </picture>
                                    <input autocomplete="off" type="text" id="captcha" name="form[captcha]" class="form__input input" placeholder="{{__('text.affiliate_code')}}" style="width: auto">
                                </div>
                            </div>
                            <button onclick="sendAjaxAffiliate()" type="button" class="form__button" id = "message_send_button">
                                <span>{{__('text.affiliate_send')}}</span>
                            </button>
                        </div>
                        <div class="form__desrc">
                            <div class="form__text-block">
                                <p>{{__('text.affiliate_contact_message_2')}}</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

@section('testimonial')
    <div class="reviews_block">
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_1')!!}</div>
                <div class="stars">
                    <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_1')}}</div>
        </div>
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_7')!!}</div>
                <div class="stars">
                    <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_7')}}</div>
        </div>
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_13')!!}</div>
                <div class="stars">
                    <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_13')}}</div>
        </div>
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_17')!!}</div>
                <div class="stars">
                    <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_17')}}</div>
        </div>
    </div>
@endsection

@endsection