
@extends($design . '.layouts.main')

@section('title', 'Moneyback')

@section('content')
<main class="main">
    <article class="raw-content">
      <h1>{{__('text.moneyback_title')}}</h1>
      <p>{!!__('text.moneyback_text')!!}</p>
    </article>
  </main>
  @endsection