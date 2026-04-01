
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
{{-- <div class="bonus_block all_padding">
    <div class="bonus1">
        <img loading="lazy" src="{{ asset("$design/images/bonus1_1.png") }}">
    </div>
    <div class="bonus2">
        <img loading="lazy" src="{{ asset("$design/images/bonus2_2.png") }}">
    </div>
</div> --}}

<div class="bonus_block all_padding big">
    <div class="bonus1">
        <a href="{{ route('home.bonus_referral_program') }}">
            <img loading="lazy" src="{{ asset("$design/images/bonus_programm.png") }}">
        </a>
    </div>
    <div class="bonus1">
        <img loading="lazy" src="{{ asset("$design/images/happy_day.png") }}">
    </div>
    <div class="bonus2">
        <img loading="lazy" src="{{ asset("$design/images/super_sale.png") }}">
    </div>
</div>
<div class="bonus_block all_padding small">
    <div class="bonus1">
        <a href="{{ route('home.bonus_referral_program') }}">
            <img loading="lazy" src="{{ asset("$design/images/bonus_1_small.png") }}">
        </a>
    </div>
    <div class="bonus1">
        <img loading="lazy" src="{{ asset("$design/images/bonus_2_small.png") }}">
    </div>
    <div class="bonus2">
        <img loading="lazy" src="{{ asset("$design/images/bonus_3_small.png") }}">
    </div>
</div>

<main class="default">
    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{ __('text.bonus_ref_menu') }}</h1>
                <div class="default__text">
                    <h1 style="font-weight: bold; font-size: 20px;">{{ __('text.bonus_page_bonus') }}</h1>
                    <br>
                    <p>{{ __('text.bonus_page_text1') }}</p>
                    <p style="line-height: 1.5">
                        {{ __('text.bonus_page_text2') }}
                    </p>
                    <ul style="line-height: 1.5">
                        {{ __('text.bonus_page_text3') }}
                        <li>1) {{ __('text.bonus_page_text4') }}</li>
                        <li>2) {{ __('text.bonus_page_text5') }}</li>
                        <li>3) {{ __('text.bonus_page_text6') }}</li>
                    </ul>
                    <br>
                    <p style="line-height: 1.8">
                        {!! __('text.bonus_page_text7') !!}
                    </p>
                    <p style="line-height: 1.8">
                        {!! __('text.bonus_page_text8') !!}
                    </p>
                    <p><b>{{ __('text.bonus_page_text9') }}</b></p>
                    <h1 style="font-weight: bold; font-size: 20px;">{{__('text.bonus_page_referral')}}</h1>
                    <br>
                    <p style="line-height: 1.5">
                        {!! __('text.bonus_page_text10') !!}
                    </p>
                    <p style="line-height: 1.5">
                        {{ __('text.bonus_page_text11') }}
                    </p>
                </div>
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