
@extends($design . '.layouts.main')

@section('title', $title)

@section('content')
<div class="container page-wrapper about_us_block">
    <main class="main">
        <article class="raw-content raw-content--narrow">
          <h1>{{__('text.about_us_title')}}</h1>
          <p>{!!__('text.about_us_text')!!}</p>
          <h2>{{__('text.about_us_title1')}}</h2>
          <p>{{__('text.about_us_text1')}}</p>
          <h2>{{__('text.about_us_title2')}}</h2>
          <div class="raw-content__lists">
            <p>{{__('text.about_us_text2_1')}}</p>
            <ul>
              <li>{{__('text.about_us_text2_2')}}</li>
              <li>{{__('text.about_us_text2_3')}}</li>
            </ul>
            <p>{{__('text.about_us_text2_4')}}</p>
            <ul>
              <li>{{__('text.about_us_text2_5')}}</li>
              <li>{{__('text.about_us_text2_6')}}</li>
            </ul>
          </div>
          <h2>{{__('text.about_us_title3')}}</h2>
          <p class="default-paragraph">{{__('text.about_us_text3_1')}}</p>
          <p>{{__('text.about_us_text3_2')}}</p>
          <p>{{__('text.about_us_text3_3')}}</p>
          <ul>
            <li>{{__('text.about_us_text3_4')}}</li>
            <li>{{__('text.about_us_text3_5')}}</li>
          </ul>
          <p>{{__('text.about_us_text3_6')}}</p>
          <h2>{{__('text.about_us_title4')}}</h2>
          <p>{{__('text.about_us_text4')}}</p>
        </article>
    </main>
</div>

@endsection

@section('rewies')
    <div class="footer-testimonials">
        <div class="testimonial card">
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
        <div class="testimonial card">
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
        <div class="testimonial card">
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
        <div class="testimonial card">
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
@endsection
