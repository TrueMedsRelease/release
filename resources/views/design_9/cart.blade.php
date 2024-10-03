@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="bonus_block all_padding">
    <div class="bonus1">
        <img src="{{ asset("$design/images/bonus1_1.png") }}">
    </div>
    <div class="bonus2">
        <img src="{{ asset("$design/images/bonus2_2.png") }}">
    </div>
</div>

<main class="basket" id="shopping_cart">

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
