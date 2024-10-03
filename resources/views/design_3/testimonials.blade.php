@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page__body">
    <div class="page__top-line top-line">
        <h1 class="top-line__title">{{__('text.testimonials_title')}}</h1>
    </div>
    <div class="page__default default-template">
        <div class="default-template__block">
            <p class="default-template__text">{!! __('text.testimonials_text') !!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_1')}} {!!__('text.testimonials_author_t_1')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_2')}} {!!__('text.testimonials_author_t_2')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_3')}} {!!__('text.testimonials_author_t_3')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_4')}} {!!__('text.testimonials_author_t_4')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_5')}} {!!__('text.testimonials_author_t_5')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_6')}} {!!__('text.testimonials_author_t_6')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_7')}} {!!__('text.testimonials_author_t_7')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_8')}} {!!__('text.testimonials_author_t_8')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_9')}} {!!__('text.testimonials_author_t_9')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_10')}} {!!__('text.testimonials_author_t_10')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_11')}} {!!__('text.testimonials_author_t_11')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_12')}} {!!__('text.testimonials_author_t_12')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_13')}} {!!__('text.testimonials_author_t_13')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_14')}} {!!__('text.testimonials_author_t_14')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_15')}} {!!__('text.testimonials_author_t_15')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_16')}} {!!__('text.testimonials_author_t_16')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_17')}} {!!__('text.testimonials_author_t_17')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_18')}} {!!__('text.testimonials_author_t_18')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_19')}} {!!__('text.testimonials_author_t_19')!!}</p>
            <p class="default-template__text">{{__('text.testimonials_t_20')}} {!!__('text.testimonials_author_t_20')!!}</p>
        </div>
    </div>
</div>
</div>
</div>

@endsection