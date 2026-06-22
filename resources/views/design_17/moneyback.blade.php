
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)
@section('header_class', 'header--secondary')

@section('content')
<div class="page-container">
    <article class="content content--page">
        <h1>{{ __('text.moneyback_title') }}</h1>
        <p class="mb-24">{!! __('text.moneyback_text') !!}</p>
    </article>
</div>
@endsection