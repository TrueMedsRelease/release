
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="container page-wrapper affiliate_block default">
    <main class="main">
        <h1 class="default__title">{{__('text.affiliate_title')}}</h1>
        <div class="message_sended hidden">
            <h2>{{__('text.affiliate_thanks')}}</h2>
            <br>
            <p>{{__('text.affiliate_sended')}}</p>
        </div>
        <form class="form contact-form form-panel">
            <fieldset class="form__fieldset form__fieldset--flex">
                <div class="form__field">
                    <input class="form__text-input input-text" type="text" id="name" required>
                    <label class="form__label form__label--text" for="name">{{__('text.affiliate_name')}}</label>
                </div>
                <div class="form__field">
                    <input class="form__text-input input-email" type="email" id="email" required>
                    <label class="form__label form__label--email" for="email">{{__('text.affiliate_email')}}</label>
                </div>
                <div class="form__field">
                    <input class="form__text-input input-text" type="text" id="jabber">
                    <label class="form__label form__label--text" for="jabber">{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}</label>
                </div>
            </fieldset>
            <div class="form__field">
                <textarea class="form__text-input input-textarea" id="message" required rows="3" cols="45"></textarea>
                <label class="form__label form__label--textarea" for="message">{{__('text.affiliate_message')}}</label>
            </div>
            <div class="form__field">
                <label class="form__label form__label--text" for="captcha">{{__('text.affiliate_code')}}</label>
                <div style="display: flex; gap: 10px; justify-content: space-between; align-items: center;">
                    <picture>
                        <img loading="lazy" src="{{ captcha_src() }}" id="captcha_image" style="border-radius: 1rem;">
                    </picture>
                    <input class="form__text-input input-text" type="text" id="captcha">
                </div>
            </div>
            <div class="form__field">
                <div class="button form__submit" onclick="sendAjaxAffiliate()">
                    <span class="button-text">{{__('text.affiliate_send')}}</span>
                    <span class="icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg") }}#arrow"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 12" fill="none" width="1em" height="1em" fill="currentColor">
                                <path d="M15.5303 6.53033C15.8232 6.23744 15.8232 5.76256 15.5303 5.46967L10.7574 0.696699C10.4645 0.403806 9.98959 0.403806 9.6967 0.696699C9.40381 0.989593 9.40381 1.46447 9.6967 1.75736L13.9393 6L9.6967 10.2426C9.40381 10.5355 9.40381 11.0104 9.6967 11.3033C9.98959 11.5962 10.4645 11.5962 10.7574 11.3033L15.5303 6.53033ZM0 6.75H15V5.25H0V6.75Z" fill="white"/>
                            </svg>
                        @endif
                    </span>
                </div>
            </div>
            <div class="form__field">
                <div class="form__caption">
                    <p>{{__('text.affiliate_contact_message_2')}}</p>
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