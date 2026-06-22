@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-container">
    <article class="content">
        <h1>{{__('text.about_us_title')}}</h1>
        <p class="mb-20">{!!__('text.about_us_text')!!}</p>
        <h2>{{__('text.about_us_title1')}}</h2>
        <p>{{__('text.about_us_text1')}}</p>
        <h2>{{__('text.about_us_title2')}}</h2>
        <p class="mb-0">{{__('text.about_us_text2_1')}}</p>
        <ul class="mb-10">
            <li>{{__('text.about_us_text2_2')}}</li>
            <li>{{__('text.about_us_text2_3')}}</li>
        </ul>
        <p class="mb-0">{{__('text.about_us_text2_4')}}</p>
        <ul>
            <li>{{__('text.about_us_text2_5')}}</li>
            <li>{{__('text.about_us_text2_6')}}</li>
        </ul>
        <h2>{{__('text.about_us_title3')}}</h2>
        <p class="mb-24">{{__('text.about_us_text3_1')}}</p>
        <p class="mb-24">{{__('text.about_us_text3_2')}}</p>
        <p class="mb-0">{{__('text.about_us_text3_3')}}</p>
        <ul class="mb-24">
            <li>{{__('text.about_us_text3_4')}}</li>
            <li>{{__('text.about_us_text3_5')}}</li>
        </ul>
        <p>{{__('text.about_us_text3_6')}}</p>
        <h2>{{__('text.about_us_title4')}}</h2>
        <p>{{__('text.about_us_text4')}}</p>
    </article>
</div>
@endsection