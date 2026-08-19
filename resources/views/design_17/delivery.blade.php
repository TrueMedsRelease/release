@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-container">
    <article class="content content--page">
        <div class="page-heading">
            <h1>{{__('text.shipping_title')}}</h1>
            <a class="button button--white button--return" href="{{ route('home.index') }}">
                <span class="icon">
                    <svg width="1em" height="1em" fill="currentColor">
                        <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#arrow-left") }}"></use>
                    </svg>
                </span>
                {{ __('text.common_back_to_chat') }}
            </a>
        </div>
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