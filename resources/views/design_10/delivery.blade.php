
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="container page-wrapper shipping_block">
    <main class="main">
        <article class="raw-content">
          <h1>{{__('text.shipping_title')}}</h1>
          <h2>{{__('text.shipping_title1')}}</h2>
          <ul>
            <li>{!!__('text.shipping_text_1')!!}</li>
            <li>{!!__('text.shipping_text_2')!!}</li>
          </ul>
          <p>{{__('text.shipping_text_3')}}</p>
          <p><strong>{{__('text.shipping_title2')}}</strong></p>
          <div class="raw-content__section-mt">
            <p>{{__('text.shipping_text_4')}}</p>
            <p>{{__('text.shipping_text_5')}}</p>
          </div>
          <div class="raw-content__section-mt">
            <ul>
              <li>{{__('text.shipping_text_6')}}</li>
              <li>{{__('text.shipping_text_7')}}</li>
              <li>{{__('text.shipping_text_8')}}</li>
              <li>{{__('text.shipping_text_9')}}</li>
            </ul>
          </div>
          <div class="raw-content__section-mt">
            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                @php
                    $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
                @endphp
                <p>{{__('text.shipping_text_10')}}<a href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{__('text.shipping_contact_us_shipping')}}</a>
                </p>
            @else
                <p>{{__('text.shipping_text_10')}}<a href="{{ route('home.contact_us', '') }}">{{__('text.shipping_contact_us_shipping')}}</a>
                </p>
            @endif
          </div>
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