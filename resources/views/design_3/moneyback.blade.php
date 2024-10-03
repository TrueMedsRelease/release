
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page__body">
    <div class="page__top-line top-line">
        <h1 class="top-line__title">{{__('text.moneyback_title')}}</h1>
    </div>
    <div class="page__default default-template">
        <div class="default-template__block">
            <p class="default-template__text">{!!__('text.moneyback_text')!!}</p>
        </div>
    </div>
</div>
</div>
</div>

@endsection