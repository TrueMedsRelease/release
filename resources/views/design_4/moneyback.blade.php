
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<h1 class="content__title title" id="scroll">{{__('text.moneyback_title')}}</h1>
<div class="text-page">
    <div class="text-page__block">
        <p>{!!__('text.moneyback_text')!!}</p>
    </div>
</div>

@endsection