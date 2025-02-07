@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-wrapper container">
    <main class="main">
        <div class="main__content">
            <div class="main__heading">
                <h1 class="h1">{{__('text.affiliate_title')}}</h1>
            </div>
            <div class="message_sended hidden">
                <h2>{{__('text.contact_us_thanks')}}</h2>
                <br>
                <p>{{__('text.contact_us_sended')}}</p>
            </div>
            <form class="form affiliate-form affiliate-form form-panel">
                <fieldset class="form__fieldset">
                    <div class="form__field text-field">
                        <input class="form__text-input input-text undefined" type="text" id="affiliate-name" name="affiliate-name" required>
                        <label class="form__label label-text" for="affiliate-name">{{__('text.affiliate_name')}}</label>
                    </div>
                    <div class="form__field text-field">
                        <input class="form__text-input input-email undefined" type="email" id="affiliate-email" name="affiliate-email" required>
                        <label class="form__label label-email" for="affiliate-email">{{__('text.affiliate_email')}}</label>
                    </div>
                    <div class="form__field text-field">
                        <input class="form__text-input input-text undefined" type="text" id="affiliate-messenger" name="affiliate-messenger">
                        <label class="form__label label-text" for="affiliate-messenger">{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}</label>
                    </div>
                </fieldset>
                <div class="form__field textarea-field">
                    <textarea class="form__text-input input-textarea" id="affiliate-message" name="affiliate-message" required></textarea>
                    <label class="form__label label-textarea" for="affiliate-message">{{__('text.affiliate_message')}}</label>
                </div>
                <div class="form__field custom-field">
                    <span class="captcha-label form__label">{{__('text.affiliate_code')}}</span>
                    <div class="captcha-wrapper">
                        <div class="captcha-img">
                            <img loading="lazy" src="{{ captcha_src() }}" id="captcha_image" style="border-radius: 1rem;">
                        </div>
                        <div class="form__field text-field">
                            <label class="form__label">
                                <input class="form__text-input" type="text" id="contact-captcha" required>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form__field submit-field">
                    <button class="button form__submit" type="button" onclick="sendAjaxAffiliate()">{{__('text.affiliate_send')}}</button>
                </div>
                <div class="form__field custom-field">
                    <div class="form__caption">
                        <p>{{__('text.affiliate_contact_message_2')}}</p>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection

@section('rewies')
<div class="container">
    <div class="footer-testimonials">
        <div class="testimonial">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_1')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_1')}}</div>
        </div>
        <div class="testimonial">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_7')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_7')}}</div>
        </div>
        <div class="testimonial">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_13')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_13')}}</div>
        </div>
        <div class="testimonial">
            <div class="testimonial__header">
                <div class="testimonial__author">{!!__('text.testimonials_author_t_17')!!}</div>
                <div class="testimonial__rating">
                    <div class="rating">
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                        <div class="rating__star"></div>
                    </div>
                </div>
            </div>
            <div class="testimonial__text">{{__('text.testimonials_t_17')}}</div>
        </div>
    </div>
</div>
@endsection