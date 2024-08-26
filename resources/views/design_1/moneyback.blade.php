
@extends($design . '.layouts.main')

@section('title', 'Moneyback')

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