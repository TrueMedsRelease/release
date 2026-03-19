
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)
@section('header_class', 'header--secondary')

@section('content')
<input type="hidden" id="error_subject" value="{{ $error_subject }}">
<div class="page-wrapper container">
    <main class="main">
        <div class="main__content">
            <div class="main__heading">
                <h1 class="h1">
                    {{__('text.contact_us_title')}}
                </h1>
            </div>
            <div class="message_sended hidden">
                <h2>{{__('text.contact_us_thanks')}}</h2>
                <br>
                <p>{{__('text.contact_us_sended')}}</p>
            </div>
            <form class="form contact-form contact-form form-panel">
                <fieldset class="form__fieldset">
                    <div class="form__field custom-field" id="contact-subject" name="contact-subject">
                        <div id="subject_block">
                            <div class="contact_subject">
                                <div id="new_subject_block">
                                    <div class="select_subject">
                                        <div class="select_header_subject">
                                            <span class="select_current_subject" curr_subject_id = "{{ $default_subject }}">{{ $subjects[$default_subject] }}</span>
                                            <div class="select_icon">
                                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                    <svg width="1em" height="1em" fill="currentColor">
                                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="none" width="1em" height="1em" fill="currentColor">
                                                        <path d="M9.47256 3.1972C9.41058 3.13471 9.33685 3.08512 9.25561 3.05127C9.17437 3.01743 9.08723 3 8.99923 3C8.91122 3 8.82408 3.01743 8.74284 3.05127C8.6616 3.08512 8.58787 3.13471 8.52589 3.1972L5.47256 6.25053C5.41058 6.31301 5.33685 6.36261 5.25561 6.39645C5.17437 6.4303 5.08723 6.44772 4.99923 6.44772C4.91122 6.44772 4.82408 6.4303 4.74284 6.39645C4.6616 6.36261 4.58787 6.31301 4.52589 6.25053L1.47256 3.1972C1.41058 3.13471 1.33685 3.08512 1.25561 3.05127C1.17437 3.01743 1.08723 3 0.999226 3C0.911218 3 0.824081 3.01743 0.742842 3.05127C0.661602 3.08512 0.587868 3.13471 0.525893 3.1972C0.401726 3.32211 0.332031 3.49107 0.332031 3.6672C0.332031 3.84332 0.401726 4.01229 0.525893 4.1372L3.58589 7.19719C3.96089 7.57172 4.46922 7.7821 4.99923 7.7821C5.52923 7.7821 6.03756 7.57172 6.41256 7.19719L9.47256 4.1372C9.59673 4.01229 9.66642 3.84332 9.66642 3.6672C9.66642 3.49107 9.59673 3.32211 9.47256 3.1972Z" fill="currentColor"/>
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="select_body_subjects">
                                            @foreach ($subjects as $id => $subject)
                                                <div class="select_item_subject" subject_id = "{{ $id }}">{{ $subject }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <label class="form__label label-text" for="contact-subject">
                            {{__('text.contact_us_subject')}}
                        </label>
                    </div>
                    <div class="form__field text-field">
                        <input class="form__text-input input-text undefined" type="text" id="contact-name" name="contact-name" required>
                        <label class="form__label label-text" for="contact-name">
                            {{__('text.contact_us_name')}}
                        </label>
                    </div>
                    <div class="form__field text-field">
                        <input class="form__text-input input-email undefined" type="email" id="contact-email" name="contact-email" required>
                        <label class="form__label label-email" for="contact-email">
                            {{__('text.contact_us_email')}}
                        </label>
                    </div>
                </fieldset>
                <div class="form__field textarea-field">
                    <textarea class="form__text-input custom-scroll input-textarea" id="contact-message" name="contact-message" required></textarea>
                    <label class="form__label label-textarea" for="contact-message">
                        {{__('text.contact_us_message')}}
                    </label>
                </div>
                <div class="form__field custom-field">
                    <div class="captcha-wrapper">
                        <div class="captcha-img">
                            <img loading="lazy" src="{{ captcha_src() }}" id="captcha_image" style="border-radius: 1rem;">
                        </div>
                        <div class="form__field text-field">
                            <label class="form__label">
                                <input class="form__text-input input-text" type="text" id="contact-captcha" required>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form__field submit-field">
                    <input class="button form__submit" type="submit" onclick="sendAjaxContact()" value="{{__('text.contact_us_send')}}">
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
@endsection