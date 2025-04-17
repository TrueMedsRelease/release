
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="bonus_block all_padding">
    <div class="bonus1">
        <img loading="lazy" src="{{ asset("$design/images/bonus1_1.png") }}">
    </div>
    <div class="bonus2">
        <img loading="lazy" src="{{ asset("$design/images/bonus2_2.png") }}">
    </div>
</div>
<main class="default">
    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{__('text.shipping_title')}}</h1>
                <div class="default__text">
                    <h3 style="font-weight: bold;">{{__('text.shipping_title1')}}</h3>
                    <br>
                    <li>{!!__('text.shipping_text_1')!!}</li>
                    <li>{!!__('text.shipping_text_2')!!}</li>
                    <p></p>
                    <p>{{__('text.shipping_text_3')}}</p>
                    <h3 style="font-weight: bold;">{{__('text.shipping_title2')}}</h3>
                    <br>
                    <p>{{__('text.shipping_text_4')}}</p>
                    <p>{{__('text.shipping_text_5')}}</p>
                    <li>{{__('text.shipping_text_6')}}</li>
                    <li>{{__('text.shipping_text_7')}}</li>
                    <li>{{__('text.shipping_text_8')}}</li>
                    <li>{{__('text.shipping_text_9')}}</li>
                    <p></p>
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        @php
                            $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
                        @endphp
                        <p>{{__('text.shipping_text_10')}}<a class="order__upgrade" style="font-size: 16px" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{__('text.shipping_contact_us_shipping')}}</a></p>
                    @else
                        <p>{{__('text.shipping_text_10')}}<a class="order__upgrade" style="font-size: 16px" href="{{ route('home.contact_us', '') }}">{{__('text.shipping_contact_us_shipping')}}</a></p>
                    @endif
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