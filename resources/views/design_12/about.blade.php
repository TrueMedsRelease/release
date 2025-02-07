@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-wrapper container">
    <main class="main">
        <article class="content content--page content--about">
            <h1>{{__('text.about_us_title')}}</h1>
            <p>{!!__('text.about_us_text')!!}</p>
            <h2>{{__('text.about_us_title1')}}</h2>
            <p>{{__('text.about_us_text1')}}</p>
            <h2>{{__('text.about_us_title2')}}</h2>
            <div class="content__section-mt">
                <p class="mb-0">{{__('text.about_us_text2_1')}}</p>
                <ul class="mb-1">
                    <li>{{__('text.about_us_text2_2')}}</li>
                    <li>{{__('text.about_us_text2_3')}}</li>
                </ul>
                <p class="mb-0">{{__('text.about_us_text2_4')}}</p>
                <ul class="mb-3">
                    <li>{{__('text.about_us_text2_5')}}</li>
                    <li>{{__('text.about_us_text2_6')}}</li>
                </ul>
            </div>
            <h2>{{__('text.about_us_title3')}}</h2>
            <p class="mb-24">{{__('text.about_us_text3_1')}}</p>
            <p class="mb-24">{{__('text.about_us_text3_2')}}</p>
            <p class="mb-0">{{__('text.about_us_text3_3')}}</p>
            <ul>
                <li>{{__('text.about_us_text3_4')}}</li>
                <li>{{__('text.about_us_text3_5')}}</li>
            </ul>
            <p class="mb-3">{{__('text.about_us_text3_6')}}</p>
            <h2>{{__('text.about_us_title4')}}</h2>
            <p>{{__('text.about_us_text4')}}</p>
        </article>
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