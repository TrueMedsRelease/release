
@extends($design . '.layouts.main')

@section('title', __('text.affiliate_title'))

@section('content')
<main class="page-text page-text--faq">
    <div class="page-text__container">
        <div class="page-text__body">
            <div class="page-text__content">
                <div class="page-text__top-row">
                    <h1 class="page-text__title title">{{__('text.affiliate_title')}}</h1>
                </div>

                <div class="message_sended hidden">
                    <h2>{{__('text.affiliate_thanks')}}</h2>
                    <br>
                    <p>{{__('text.affiliate_sended')}}</p>
                </div>

                <div class="page-text__inner">
                    <form class="contact-us-form" method="post">
                        <div class="contact-us-form__inner">
                            <div class="contact-us-form__rows">
                                <div class="contact-us-form__row">
                                    <div class="contact-us-form__input">
                                        <label class="contact-us-form__label" for="name">{{__('text.affiliate_name')}}</label>
                                        <input id="name" autocomplete="off" type="text" name="form[]" class="input" onkeyup="undisabled('affiliate')">
                                    </div>
                                </div>
                                <div class="contact-us-form__row">
                                    <div class="contact-us-form__input">
                                        <label class="contact-us-form__label" for="email">{{__('text.affiliate_email')}}</label>
                                        <input id="email" autocomplete="off" type="email" name="form[]" class="input" onkeyup="undisabled('affiliate')">
                                    </div>
                                </div>
                                <div class="contact-us-form__row">
                                    <div class="contact-us-form__input">
                                        <label class="contact-us-form__label" for="jabber">{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}</label>
                                        <input id="jabber" autocomplete="off" type="text" name="form[]" class="input">
                                    </div>
                                </div>
                                <div class="contact-us-form__row">
                                    <div class="contact-us-form__input">
                                        <label class="contact-us-form__label" for="message">{{__('text.affiliate_message')}}</label>
                                        <textarea id="message" autocomplete="off" type="text" name="form[]" class="input"></textarea>
                                    </div>
                                </div>
                                <div class="contact-us-form__row">
                                    <div class="contact-us-form__input">
                                        <label class="contact-us-form__label" for="captcha">{{__('text.affiliate_code')}}</label>
                                        <input id="captcha" autocomplete="off" type="text" name="form[]" class="input" onkeyup="undisabled('affiliate')">
                                    </div>
                                    <div class="contact-us-form__captcha">
                                        <picture><source srcset="/captcha" type="image/webp"><img src="/captcha" width="140" height="70" alt="{{__('text.affiliate_code')}}"></picture>
                                    </div>
                                </div>
                            </div>
                            <div class="contact-us-form__descr">
                                <p>{{__('text.affiliate_contact_message')}}</p>
                            </div>
                        </div>
                        <button type="submit" class="contact-us-form__button button" id="affiliate_send_button" disabled="disabled">{{__('text.affiliate_send')}}</button>
                    </form>
                </div>
            </div>
            <aside class="page-text__sidebar">
                <div class="page-text__offers">
                    <div class="page-text__offer">
                        <picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/01.png") }}" alt=""></picture>
                    </div>
                    <div class="page-text__offer">
                        <picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/02.png") }}" alt=""></picture>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <section class="subscribe__container">
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
    </section>

    <section class="page__testimonials testimonials">
        <div class="testimonials__container">
            <h2 class="visually-hidden">Title for seo</h2>
            <div class="testimonials__body">
                <div class="testimonials__item">
                    <div class="testimonials__row">
                        <div class="testimonials__name">{!!__('text.testimonials_author_t_1')!!}</div>
                        <div class="testimonials__stars">
                            <svg width="90" height="14">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                            </svg>
                        </div>
                    </div>
                    <p class="testimonials__text">{{__('text.testimonials_t_1')}}</p>
                </div>

                <div class="testimonials__item">
                    <div class="testimonials__row">
                        <div class="testimonials__name">{!!__('text.testimonials_author_t_2')!!}</div>
                        <div class="testimonials__stars">
                            <svg width="90" height="14">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                            </svg>
                        </div>
                    </div>
                    <p class="testimonials__text">{{__('text.testimonials_t_2')}}</p>
                </div>

                <div class="testimonials__item">
                    <div class="testimonials__row">
                        <div class="testimonials__name">{!!__('text.testimonials_author_t_3')!!}</div>
                        <div class="testimonials__stars">
                            <svg width="90" height="14">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                            </svg>
                        </div>
                    </div>
                    <p class="testimonials__text">{{__('text.testimonials_t_13')}}</p>
                </div>

                <div class="testimonials__item">
                    <div class="testimonials__row">
                        <div class="testimonials__name">{!!__('text.testimonials_author_t_4')!!}</div>
                        <div class="testimonials__stars">
                            <svg width="90" height="14">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                            </svg>
                        </div>
                    </div>
                    <p class="testimonials__text">{{__('text.testimonials_t_4')}}</p>
                </div>
            </div>
            <a href="{{ route('home.testimonials') }}" class="testimonials__button button">
                <span>{{__('text.common_testimonials_main_menu_item')}}</span>
                <svg width="16" height="16">
                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-right") }}"></use>
                </svg>
            </a>
        </div>
    </section>
</main>

@endsection