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
<main class="default">
    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{__('text.testimonials_title')}}</h1>
                <div class="default__reviews">
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_1')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_1')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_2')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_2')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_3')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_3')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_4')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_4')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_5')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_5')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_6')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_6')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_7')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_7')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_8')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_8')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_9')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_9')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_10')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_10')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_11')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_11')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_12')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_12')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_13')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_13')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_14')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_14')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_15')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_15')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_16')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_16')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_17')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_17')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_18')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_18')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_19')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_19')}}</p>
                        </div>
                    </div>
                    <div class="default__review">
                        <div class="default__review_text">
                            <p class="author">{!!__('text.testimonials_author_t_20')!!}</p>
                            <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                        </div>
                        <div>
                            <p class="author_text">{{__('text.testimonials_t_20')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection