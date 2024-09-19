
@extends($design . '.layouts.main')

@section('title', $title)

@section('content')
<div class="container page-wrapper contact_us_block default">
    <main class="main">
        <h1 class="default__title">{{__('text.contact_us_title')}}</h1>
        <div class="message_sended hidden">
            <h2>{{__('text.contact_us_thanks')}}</h2>
            <br>
            <p>{{__('text.contact_us_sended')}}</p>
        </div>
        <form class="form contact-form form-panel">
            <fieldset class="form__fieldset form__fieldset--flex">
                <div class="form__field">
                    <input class="form__text-input input-text" type="text" id="name" required>
                    <label class="form__label form__label--text" for="name">{{__('text.contact_us_name')}}</label>
                </div>
                <div class="form__field">
                    <input class="form__text-input input-email" type="email" id="email_form" required>
                    <label class="form__label form__label--email" for="email_form">{{__('text.contact_us_email')}}</label>
                </div>
                <div class="form__field">
                    <input class="form__text-input input-text" type="text" id="subject">
                    <label class="form__label form__label--text" for="subject">{{__('text.contact_us_subject')}}</label>
                </div>
            </fieldset>
            <div class="form__field">
                <textarea class="form__text-input input-textarea" id="message" required rows="3" cols="45"></textarea>
                <label class="form__label form__label--textarea" for="message">{{__('text.contact_us_message')}}</label>
            </div>
            <div class="form__field">
                <label class="form__label form__label--text" for="captcha">{{__('text.contact_us_code')}}</label>
                <div style="display: flex">
                    <picture>
                        <source srcset="{{ captcha_src() }}" type="image/webp">
                        <img src="{{ captcha_src() }}">
                    </picture>
                    <div class="mt-2"></div>
                    <input class="form__text-input input-text form-control" type="text" id="captcha" name="captcha" >
                </div>
            </div>
            <div class="form__field">
                <div class="button form__submit" onclick="sendAjaxContact()">
                    <span class="button-text">{{__('text.contact_us_send')}}</span>
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                              <use href="{{ asset("$design/svg/icons/sprite.svg#arrow") }}"></use>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="form__field">
                <div class="form__caption">
                    <p>{{__('text.contact_us_describe1')}}</p>
                    <p>{{__('text.contact_us_describe2')}}</p>
                    <p>{{__('text.contact_us_describe3')}}</p>
                </div>
            </div>
        </form>
    </main>
</div>

@endsection

@section('rewies')
    <div class="footer-testimonials">
        <div class="testimonial card">
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
        <div class="testimonial card">
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
        <div class="testimonial card">
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
        <div class="testimonial card">
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
@endsection