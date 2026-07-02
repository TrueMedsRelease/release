@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
    <h1 class="main-heading js-chat-start-heading">
        <span class="main-heading__title">Its’ True Meds Bot for buying Drugs</span>
        <span class="main-heading__caption">Easier, Safer, Faster</span>
    </h1>
@endsection
