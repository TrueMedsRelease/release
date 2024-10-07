
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')

<div class="page__body">
    <div class="page__top-line top-line">
        <h1 class="top-line__title">{{__('text.about_us_title')}}</h1>
    </div>
    <div class="page__default default-template">
        <div class="default-template__block default-template__block--with-border">
            <p class="default-template__text">{!!__('text.about_us_text')!!}</p>
        </div>
        <div class="default-template__block default-template__block--with-border">
            <h2 class="default-template__caption">{{__('text.about_us_title1')}}</h2>
            <p class="default-template__text">{{__('text.about_us_text1')}}</p>
        </div>
        <div class="default-template__block default-template__block--with-border">
            <h2 class="default-template__caption">{{__('text.about_us_title2')}}</h2>
            <p class="default-template__text default-template__text--no-offset">{{__('text.about_us_text2_1')}}</p>
            <ul class="default-template__list">
                <li>{{__('text.about_us_text2_2')}}</li>
                <li>{{__('text.about_us_text2_3')}}</li>
            </ul>
            <p class="default-template__text default-template__text--no-offset">{{__('text.about_us_text2_4')}}</p>
            <ul class="default-template__list">
                <li>{{__('text.about_us_text2_5')}}</li>
                <li>{{__('text.about_us_text2_6')}}</li>
            </ul>
        </div>
        <div class="default-template__block default-template__block--with-border">
            <h2 class="default-template__caption">{{__('text.about_us_title3')}}</h2>
            <p class="default-template__text">{{__('text.about_us_text3_1')}}</p>
            <p class="default-template__text">{{__('text.about_us_text3_2')}}</p>
            <p class="default-template__text default-template__text--no-offset">{{__('text.about_us_text3_3')}}</p>
            <ul class="default-template__list">
                <li>{{__('text.about_us_text3_4')}}</li>
                <li>{{__('text.about_us_text3_5')}}</li>
            </ul>
            <p class="default-template__text">{{__('text.about_us_text3_6')}}</p>
        </div>
        <div class="default-template__block default-template__block--with-border">
            <h2 class="default-template__caption">{{__('text.about_us_title4')}}</h2>
            <p class="default-template__text">{{__('text.about_us_text4')}}</p>
        </div>
    </div>
</div>
</div>
</div>

@endsection