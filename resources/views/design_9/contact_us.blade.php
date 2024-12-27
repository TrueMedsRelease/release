@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<input type="hidden" id="error_subject" value="{{ $error_subject }}">
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
                                <div class="form_rows_top">
                                    <div class="form__row">
                                        <label for="subject" class="form__label">{{__('text.contact_us_subject')}}</label>
                                        <div class="form__field custom-field" id="subject" name="subject">
                                            <div id="subject_block">
                                                <div class="contact_subject">
                                                    <div id="new_subject_block">
                                                        <div class="select_subject">
                                                            <div class="select_header_subject">
                                                                <span class="select_current_subject" curr_subject_id = "{{ $default_subject }}">{{ $subjects[$default_subject] }}</span>
                                                                <div class="select_icon">
                                                                    <img src="{{ asset("$design/images/icons/arrow_down_black.svg") }}">
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
                                        </div>
                                    </div>
                                    <div class="form__row">
                                        <label for="name" class="form__label">{{__('text.contact_us_name')}}</label>
                                        <input data-required id = "name" autocomplete="off" type="text" name="form[name]" data-error="" placeholder="{{__('text.contact_us_name')}}" class="form__input input">
                                    </div>
                                    <div class="form__row">
                                        <label for="email_form" class="form__label">{{__('text.contact_us_email')}}</label>
                                        <input data-required id="email_form" autocomplete="off" type="text" name="form[email_form]" data-error="" placeholder="{{__('text.contact_us_email')}}" class="form__input input">
                                    </div>
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
                                        <img loading="lazy" id="captcha_image" src="{{ captcha_src() }}" style="border-radius: 14px;">
                                    </picture>
                                    <input autocomplete="off" type="text" id = "captcha" name="form[captcha]" data-error="" placeholder="{{__('text.contact_us_code')}}" class="form__input input" style="width: auto">
                                </div>
                            </div>
                            <input onclick="sendAjaxContact()" type="button" name="form[submit]" value="{{__('text.contact_us_send')}}"  id="message_send_button" class="form__button">
                        </div>
                        <div class="form__desrc">
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