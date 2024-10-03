
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<h1 class="content__title title">{{__('text.shipping_title')}}</h1>
<div class="text-page">
    <div class="text-page__block">
    	<h3>{{__('text.shipping_title1')}}</h3>
    	<ul class="text-page__list ">
    		<li>{!!__('text.shipping_text_1')!!}</li>
    		<li>{!!__('text.shipping_text_2')!!}</li>
    	</ul>
    	<p>{{__('text.shipping_text_3')}}</p>
    	<p>{{__('text.shipping_title2')}}</p>
    	<p>{{__('text.shipping_text_4')}}</p>
    	<p>{{__('text.shipping_text_5')}}</p>
    	<ul class="text-page__list ">
    		<li>{{__('text.shipping_text_6')}}</li>
    		<li>{{__('text.shipping_text_7')}}</li>
    		<li>{{__('text.shipping_text_8')}}</li>
    		<li>{{__('text.shipping_text_9')}}</li>
    	</ul>
    	<p>{{__('text.shipping_text_10')}}<a href="{{ route('home.contact_us') }}">{{__('text.shipping_contact_us_shipping')}}</a></p>
    </div>
</div>

@endsection