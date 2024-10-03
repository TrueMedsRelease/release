
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')

<h1 class="content__title title" id="scroll">{{__('text.about_us_title')}}</h1>
<div class="text-page">
    <div class="text-page__block">
        {!!__('text.about_us_text')!!}
    	<h2 class="text-page__label">{{__('text.about_us_title1')}}</h2>
    	<p>{{__('text.about_us_text1')}}</p>
    	<h2 class="text-page__label">{{__('text.about_us_title2')}}</h2>
    	<h3>{{__('text.about_us_text2_1')}}</h3>
    	<ul class="text-page__list ">
    		<li>{{__('text.about_us_text2_2')}}</li>
    		<li>{{__('text.about_us_text2_3')}}</li>
    	</ul>
    	<h3>{{__('text.about_us_text2_4')}}</h3>
    	<ul class="text-page__list ">
    		<li>{{__('text.about_us_text2_5')}}</li>
    		<li>{{__('text.about_us_text2_6')}}</li>
    	</ul>
    	<h2 class="text-page__label">{{__('text.about_us_title3')}}</h2>
    	<p>{{__('text.about_us_text3_1')}}</p>
    	<p>{{__('text.about_us_text3_2')}}</p>
    	<h3>{{__('text.about_us_text3_3')}}</h3>
    	<ul class="text-page__list ">
    		<li>{{__('text.about_us_text3_4')}}</li>
    		<li>{{__('text.about_us_text3_5')}}</li>
    	</ul>
    	<p>{{__('text.about_us_text3_6')}}</p>
    	<h2 class="text-page__label">{{__('text.about_us_title4')}}</h2>
    	<p>{{__('text.about_us_text4')}}</p>
    </div>
</div>

@endsection