
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<input type="hidden" id="error_subject" value="{{ $error_subject }}">
<div class="page__body">
    <div class="page__top-line top-line">
        <h1 class="top-line__title">{{__('text.contact_us_title')}}</h1>
    </div>
    <div class="message_sended hidden">
        <h2>{{__('text.contact_us_thanks')}}</h2>
        <br>
        <p>{{__('text.contact_us_sended')}}</p>
    </div>
    <div class="page__contact contact-us">
        <form class="contact-us__form form" method="POST">
            <div class="form__row">
                <div class="form__input">
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
                <div class="form__input">
                    <input data-required id = "name" autocomplete="off" type="text" name="form[name]" data-error="" placeholder="{{__('text.contact_us_name')}}" class="input">
                </div>
                <div class="form__input">
                    <input data-required="email" id = "email" autocomplete="off" type="text" name="form[email]" data-error="Error" placeholder="{{__('text.contact_us_email')}}" class="input">
                </div>
            </div>
            <div class="form__row">
                <textarea autocomplete="off" id = "message" type="text" name="form[message]" data-error="Error" placeholder="{{__('text.contact_us_message')}}" class="input"></textarea>
            </div>
            <div class="form__row captcha">
            <picture>
                <img loading="lazy" id="captcha_image" src="{{ captcha_src() }}" style="border-radius: 10px;">
            </picture>
            <div class="form__input">
                    <input autocomplete="off" type="text" id = "captcha" name="form[captcha]" data-error="Error" placeholder="{{__('text.contact_us_code')}}" class="input">
                </div>
            </div>
            <button onclick="sendAjaxContact()" type="button" class="form__button">{{__('text.contact_us_send')}}</button>
        </form>
        <div class="contact-us__descr">
            <p>{{__('text.contact_us_describe1')}}</p>
            <p>{{__('text.contact_us_describe2')}}</p>
            <p>{{__('text.contact_us_describe3')}}</p>
        </div>
    </div>
</div>

@endsection