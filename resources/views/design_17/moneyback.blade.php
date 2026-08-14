
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)
@section('header_class', 'header--secondary')

@section('content')
<div class="page-container">
    <article class="content content--page">
        <div class="page-heading">
            <h1>{{ __('text.moneyback_title') }}</h1>
            <a class="button button--white button--return" href="{{ route('home.index') }}">
                <span class="icon">
                    <svg width="1em" height="1em" fill="currentColor">
                        <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#arrow-left") }}"></use>
                    </svg>
                </span>
                {{ __('text.common_back_to_chat') }}
            </a>
        </div>
        <p class="mb-24">{!! __('text.moneyback_text') !!}</p>
    </article>
</div>
@endsection