
@extends($design . '.layouts.main')

@section('title', __('text.about_us_title'))

@section('content')
<div class="bonus_block all_padding">
    <div class="bonus1">
        <img src="{{ asset("$design/images/bonus1_1.png") }}">
    </div>
    <div class="bonus2">
        <img src="{{ asset("$design/images/bonus2_2.png") }}">
    </div>
</div>
<main class="default">
    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{__('text.about_us_title')}}</h1>
                <div class="default__text">
                    {!!__('text.about_us_text')!!}
                <h2 style="font-weight: bold;">{{__('text.about_us_title1')}}</h2>
                <p>{{__('text.about_us_text1')}}</p>
                <h2 style="font-weight: bold;">{{__('text.about_us_title2')}}</h2>
                <ul>{{__('text.about_us_text2_1')}}</ul>
                    <li>{{__('text.about_us_text2_2')}}</li>
                    <li>{{__('text.about_us_text2_3')}}</li>
                    <p></p>
                <ul>{{__('text.about_us_text2_4')}}</ul>
                    <li>{{__('text.about_us_text2_5')}}</li>
                    <li>{{__('text.about_us_text2_6')}}</li>
                    <p></p>
                <h2 style="font-weight: bold;">{{__('text.about_us_title3')}}</h2>
                <p>{{__('text.about_us_text3_1')}}</p>
                <p>{{__('text.about_us_text3_2')}}</p>
                <ul>{{__('text.about_us_text3_3')}}</ul>
                    <li>{{__('text.about_us_text3_4')}}</li>
                    <li>{{__('text.about_us_text3_5')}}</li>
                    <p></p>
                <p>{{__('text.about_us_text3_6')}}</p>
                <h2 style="font-weight: bold;">{{__('text.about_us_title4')}}</h2>
                <p>{{__('text.about_us_text4')}}</p>
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
                    <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_1')}}</div>
        </div>
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_7')!!}</div>
                <div class="stars">
                    <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_7')}}</div>
        </div>
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_13')!!}</div>
                <div class="stars">
                    <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_13')}}</div>
        </div>
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_17')!!}</div>
                <div class="stars">
                    <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_17')}}</div>
        </div>
    </div>
@endsection

@endsection