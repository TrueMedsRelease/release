
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page__body">
    <div class="page__top-line top-line">
        <h1 class="top-line__title">{{__('text.shipping_title')}}</h1>
    </div>
    <div class="page__default default-template">
        <div class="default-template__block">
        <h3 style="font-weight: bold;">{{__('text.shipping_title1')}}</h3>
            <br>
            <li>{!!__('text.shipping_text_1')!!}</li>
            <li>{!!__('text.shipping_text_2')!!}</li>
            <br>
            <p>{{__('text.shipping_text_3')}}</p>
            <br>
            <h3 style="font-weight: bold;">{{__('text.shipping_title2')}}</h3>
            <br>
            <p>{{__('text.shipping_text_4')}}</p>
            <br>
            <p>{{__('text.shipping_text_5')}}</p>
            <br>
            <li>{{__('text.shipping_text_6')}}</li>
            <li>{{__('text.shipping_text_7')}}</li>
            <li>{{__('text.shipping_text_8')}}</li>
            <li>{{__('text.shipping_text_9')}}</li>
            <br>
            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                @php
                    $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
                @endphp
                <p>{{__('text.shipping_text_10')}}<a class = "order__upgrade" style="font-size: 15px" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{__('text.shipping_contact_us_shipping')}}</a></p>
            @else
                <p>{{__('text.shipping_text_10')}}<a class = "order__upgrade" style="font-size: 15px" href="{{ route('home.contact_us', '') }}">{{__('text.shipping_contact_us_shipping')}}</a></p>
            @endif
        </div>
    </div>
</div>
</div>
</div>

@endsection