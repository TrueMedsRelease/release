
@extends($design . '.layouts.main')

@section('title', $title)

@section('content')
<main class="default">
    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{__('text.contact_us_title')}}</h1>
                <div class="message_sended hidden">
                    <h2>{{__('text.contact_us_thanks')}}</h2>
                    <br>
                    <p>{{__('text.contact_us_sended')}}</p>
                </div>
                <form class="form" id = "message_send_form" method="post">
                    <div class="form__body">
                        <div class="form__inner">
                            <div class="form__default-rows">
                                <div class="form__row">
                                    <label for="name" class="form__label">{{__('text.contact_us_name')}}</label>
                                    <input data-required id = "name" autocomplete="off" type="text" name="form[name]" data-error="" placeholder="{{__('text.contact_us_name')}}" class="form__input input">
                                </div>
                                <div class="form__row">
                                    <label for="email_form" class="form__label">{{__('text.contact_us_email')}}</label>
                                    <input data-required id="email_form" autocomplete="off" type="text" name="form[email_form]" data-error="" placeholder="{{__('text.contact_us_email')}}" class="form__input input">
                                </div>
                                <div class="form__row">
                                    <label for="subject" class="form__label">{{__('text.contact_us_subject')}}</label>
                                    <input autocomplete="off" id = "subject" type="text" name="form[subject]" data-error="" placeholder="{{__('text.contact_us_subject')}}" class="form__input input">
                                </div>
                                <div class="form__row form__row--top-alignment">
                                    <label for="message" class="form__label">{{__('text.contact_us_message')}}</label>
                                    <textarea autocomplete="off" id = "message" type="text" name="form[message]" data-error="" placeholder="{{__('text.contact_us_message')}}" class="form__input input"></textarea>
                                </div>
                            </div>
                            <div class="form__row form__row--captcha">
                                <label for="name" class="form__label">{{__('text.contact_us_code')}}</label>
                                <div class="form__input">
                                    <picture>
                                        <source srcset="{{ captcha_src() }}" type="image/webp">
                                        <img src="{{ captcha_src() }}">
                                    </picture>
                                    <input autocomplete="off" type="text" id = "captcha" name="form[captcha]" data-error="" placeholder="{{__('text.contact_us_code')}}" class="form__input input">
                                </div>
                            </div>
                        </div>
                        <div class="form__desrc">
                            <div class="contact_us_phones form__text-block">
                                <p>{{__('text.phones_title_support')}}</p>
                                <a href="tel:{{__('text.phones_title_phone_1')}}">{{__('text.phones_title_phone_1')}}</a>
                                <br>
                                <a href="tel:{{__('text.phones_title_phone_1')}}">{{__('text.phones_title_phone_2')}}</a>
                            </div>
                            <div class="form__text-block">
                                <p>{{__('text.contact_us_describe1')}}</p>
                            </div>
                            <div class="form__text-block">
                                <p>{{__('text.contact_us_describe2')}}</p>
                            </div>
                            <div class="form__text-block">
                                <p>{{__('text.contact_us_describe3')}}</p>
                            </div>
                        </div>
                    </div>
                    <input onclick="sendAjaxContact()" type="button" name="form[submit]" value="{{__('text.contact_us_send')}}"  id = "message_send_button" class="form__button"></input>
                </form>
            </div>
            <aside class="default__aside">
                <div class="default__offers">
                    <a href="#" class="default__item-offer">
                        <picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/01.jpg") }}" alt=""></picture>
                    </a>
                    <a href="#" class="default__item-offer">
                        <picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/02.jpg") }}" alt=""></picture>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</main>
@endsection

@section('reviews')

<section class="reviews">
    <div class="reviews__container">
        <div class="reviews__body">
            <div class="reviews__slider">
                <div class="reviews__swiper">
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_1')!!}</div>
                            <div class="reviews__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_1')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_2')!!}</div>
                            <div class="reviews__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_2')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_3')!!}</div>
                            <div class="reviews__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_3')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_4')!!}</div>
                            <div class="reviews__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_4')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_5')!!}</div>
                            <div class="reviews__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_5')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_6')!!}</div>
                            <div class="reviews__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_6')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_7')!!}</div>
                            <div class="reviews__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_7')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_8')!!}</div>
                            <div class="reviews__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_8')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_9')!!}</div>
                            <div class="reviews__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_9')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_10')!!}</div>
                            <div class="reviews__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_10')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_11')!!}</div>
                            <div class="reviews__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_11')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_12')!!}</div>
                            <div class="reviews__stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_12')}}</div>
                    </div>
                </div>
            </div>
            <div class="reviews__controls">
                <button type="button" class="reviews__arrow reviews__arrow--prev">
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-prev") }}"></use>
                    </svg>
                    <span>{{__('text.testimonials_prev')}}</span>
                </button>
                <button type="button" class="reviews__arrow reviews__arrow--next">
                    <span>{{__('text.testimonials_next')}}</span>
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-next") }}"></use>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>

@endsection