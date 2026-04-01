@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-wrapper container">
    <main class="main">
        <article class="content content--page content--about">
            <h1>{{__('text.bonus_ref_menu')}}</h1>
            <h2>{{ __('text.bonus_page_bonus') }}</h2>
            <p>{{ __('text.bonus_page_text1') }}</p>
            <p style="line-height: 1.5">
                {{ __('text.bonus_page_text2') }}
            </p>
            <div class="content__section-mt">
                <p class="mb-0">{{ __('text.bonus_page_text3') }}</p>
                <ul style="line-height: 1.5">
                    <li>{{ __('text.bonus_page_text4') }}</li>
                    <li>{{ __('text.bonus_page_text5') }}</li>
                    <li>{{ __('text.bonus_page_text6') }}</li>
                </ul>
            </div>
            <p style="line-height: 1.8">
                {!! __('text.bonus_page_text7') !!}
            </p>
            <p style="line-height: 1.8">
                {!! __('text.bonus_page_text8') !!}
            </p>
            <p><b>{{ __('text.bonus_page_text9') }}</b></p>

            <h2>{{ __('text.bonus_page_referral') }}</h2>
            <p style="line-height: 1.5">
                {!! __('text.bonus_page_text10') !!}
            </p>
            <p style="line-height: 1.5">
                {{ __('text.bonus_page_text11') }}
            </p>
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