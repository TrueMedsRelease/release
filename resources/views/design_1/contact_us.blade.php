
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<input type="hidden" id="error_subject" value="{{ $error_subject }}">
<section class="page__form-block form-block"  id = "scroll">
    <div class="message_sended hidden">
    <h2>{{__('text.contact_us_thanks')}}</h2>
    <br>
    <p>{{__('text.contact_us_sended')}}</p>
    </div>
    <div class="form-block__container">
        <h2 class="form-block__title title">{{__('text.contact_us_title')}}</h2>
        <div class="form-block__body">
            <div class="form-block__descr">
                <p>{{__('text.contact_us_describe1')}}</p>
                <p>{{__('text.contact_us_describe2')}}</p>
                <p>{{__('text.contact_us_describe3')}}</p>
            </div>
        <form action="" id = "message_send_form" class="form-block__form form" method="post">
            <div class="form__default-rows">
                <div class="form__row">
                    <label for="subject" class="form__label">{{__('text.contact_us_subject')}}</label>
                    <div class="form__field custom-field" id="contact-subject" name="contact-subject">
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
                    <input data-required autocomplete="off" type="text" id = "name" name="form[name]" data-error="" placeholder="{{__('text.contact_us_name')}}" class="form__input input">
                </div>
                <div class="form__row">
                    <label for="email" class="form__label">{{__('text.contact_us_email')}}</label>
                    <input data-required="email" autocomplete="off" type="text" id = "email" name="form[email]" data-error="" placeholder="{{__('text.contact_us_email')}}" class="form__input input">
                </div>
                <div class="form__row form__row--top-alignment">
                    <label for="name" class="form__label">{{__('text.contact_us_message')}}</label>
                    <textarea autocomplete="off" type="text" id = "message" name="form[message]" data-error="" placeholder="{{__('text.contact_us_message')}}" class="form__input input"></textarea>
                </div>
            </div>
            <div class="form__row form__row--captcha">
                <label for="captcha" class="form__label">{{__('text.contact_us_code')}}</label>
                <div class="form__input">
                    <picture>
                        <img loading="lazy" id="captcha_image" src="{{ captcha_src() }}" style="border-radius: 10px;">
                    </picture>
                    <input autocomplete="off" type="text" id = "captcha" name="form[captcha]" data-error="" placeholder="{{__('text.contact_us_code')}}" class="form__input input">
                </div>
            </div>
            <input onclick="sendAjaxContact()" type="button" name="form[submit]" value="{{__('text.contact_us_send')}}"  id = "message_send_button" class="form__button button button--primary button--with-arrow button--send"></button>
        </form>
    </div>
</section>

@endsection