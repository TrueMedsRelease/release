@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<input type="hidden" id="error_subject" value="{{ $error_subject }}">
<div class="page-wrapper container">
    <main class="main">
        <div class="main__content">
            <div class="main__heading">
                <h1 class="h1">{{__('text.contact_us_title')}}</h1>
            </div>
            <div class="message_sended hidden">
                <h2>{{__('text.contact_us_thanks')}}</h2>
                <br>
                <p>{{__('text.contact_us_sended')}}</p>
            </div>
            <form class="form contact-form contact-form form-panel">
                <fieldset class="form__fieldset">
                    <div class="form__field custom-field">
                        <div class="form__label">{{__('text.contact_us_subject')}}</div>
                        <div class="select-wrapper">
                            <select class="select" id="subject_text">
                                @foreach ($subjects as $id => $subject)
                                    <option value="{{ $id }}" @if ($default_subject == $id) selected @endif>{{ $subject }}</option>
                                @endforeach
                            </select>
                            <span class="icon select-wrapper__chevron">
                                <img src="{{ asset($design . '/images/icons/arrow_contact.svg') }}">
                            </span>
                        </div>
                    </div>
                    <div class="form__field text-field">
                        <input class="form__text-input input-text undefined" type="text" id="contact-name" name="contact-name" required>
                        <label class="form__label label-text" for="contact-name">{{__('text.contact_us_name')}}</label>
                    </div>
                    <div class="form__field text-field">
                        <input class="form__text-input input-email undefined" type="email" id="contact-email" name="contact-email" required>
                        <label class="form__label label-email" for="contact-email">{{__('text.contact_us_email')}}</label>
                    </div>
                </fieldset>
                <div class="form__field textarea-field">
                    <textarea class="form__text-input input-textarea" id="contact-message" name="contact-message" required></textarea>
                    <label class="form__label label-textarea" for="contact-message">{{__('text.contact_us_message')}}</label>
                </div>
                <div class="form__field custom-field">
                    <span class="captcha-label form__label">{{__('text.contact_us_code')}}</span>
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
                    <button class="button form__submit" type="button" onclick="sendAjaxContact()">{{ __('text.contact_us_send') }}</button>
                </div>
                <div class="form__field custom-field">
                    <div class="form__caption">
                        <p>{{__('text.contact_us_describe1')}}</p>
                        <p>{{__('text.contact_us_describe2')}}</p>
                        <p>{{__('text.contact_us_describe3')}}</p>
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