
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<section class="page__text-block text-block">
    <div class="text-block__container">
        <h2 class="text-block__title title" id = "scroll">{{__('text.moneyback_title')}}</h2>
            <div class="text-block__body">
                {!!__('text.moneyback_text')!!}
            </div>
        </div>
</section>

@endsection