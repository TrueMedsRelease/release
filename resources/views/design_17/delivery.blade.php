@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-container">
    <article class="content content--page">
        <h1>{{__('text.shipping_title')}}</h1>
        <h2>{{__('text.shipping_title1')}}</h2>
        <ul class="mb-24">
            <li>{!!__('text.shipping_text_1')!!}</li>
            <li>{!!__('text.shipping_text_2')!!}</li>
        </ul>
        <p class="mb-24">{{__('text.shipping_text_3')}}</p>
        <p><strong>{{__('text.shipping_title2')}}</strong></p>
        <p class="mb-24">
            <p>{{__('text.shipping_text_4')}}</p>
            <p>{{__('text.shipping_text_5')}}</p>
        </p>
        <ul class="mb-24">
            <li>{{__('text.shipping_text_6')}}</li>
            <li>{{__('text.shipping_text_7')}}</li>
            <li>{{__('text.shipping_text_8')}}</li>
            <li>{{__('text.shipping_text_9')}}</li>
        </ul>
        <p>
            {{__('text.shipping_text_10')}}
            <a href="{{ route('home.contact_us', '') }}">{{__('text.shipping_contact_us_shipping')}}</a>
        </p>
    </article>
</div>
@endsection