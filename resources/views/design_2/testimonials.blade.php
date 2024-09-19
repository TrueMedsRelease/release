@extends($design . '.layouts.main')

@section('title', $title)

@section('content')
<main class="default">
    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{__('text.testimonials_title')}}</h1>
                <div class="default__desrc-reviews">
                    <p>{!! __('text.testimonials_text') !!}</p>
                </div>
                <div class="default__reviews">
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_1')}} {!!__('text.testimonials_author_t_1')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_2')}} {!!__('text.testimonials_author_t_2')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_3')}} {!!__('text.testimonials_author_t_3')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_4')}} {!!__('text.testimonials_author_t_4')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_5')}} {!!__('text.testimonials_author_t_5')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_6')}} {!!__('text.testimonials_author_t_6')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_7')}} {!!__('text.testimonials_author_t_7')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_8')}} {!!__('text.testimonials_author_t_8')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_9')}} {!!__('text.testimonials_author_t_9')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_10')}} {!!__('text.testimonials_author_t_10')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_11')}} {!!__('text.testimonials_author_t_11')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_12')}} {!!__('text.testimonials_author_t_12')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_13')}} {!!__('text.testimonials_author_t_13')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_14')}} {!!__('text.testimonials_author_t_14')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_15')}} {!!__('text.testimonials_author_t_15')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_16')}} {!!__('text.testimonials_author_t_16')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_17')}} {!!__('text.testimonials_author_t_17')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_18')}} {!!__('text.testimonials_author_t_18')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_19')}} {!!__('text.testimonials_author_t_19')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_20')}} {!!__('text.testimonials_author_t_20')!!}</p>
                    </div>
                </div>
            </div>
            <aside class="default__aside">
                <div class="default__offers">
                    <a href="#" class="default__item-offer">
                        <picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/01.jpg") }}" alt=""></picture>
                    </a>
                    <a href="#" class="default__item-offer">
                        <picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/02.jpg") }}" alt=""></picture>
                    </a>
            </aside>
        </div>
    </div>
</main>

@endsection