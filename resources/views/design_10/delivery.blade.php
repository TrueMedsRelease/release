
@extends($design . '.layouts.main')

@section('title', 'Delivery')

@section('content')
<main class="main">
    <article class="raw-content">
      <h1>{{__('text.shipping_title')}}</h1>
      <h2>{{__('text.shipping_title1')}}</h2>
      <ul>
        <li>{!!__('text.shipping_text_1')!!}</li>
        <li>{!!__('text.shipping_text_2')!!}</li>
      </ul>
      <p>{{__('text.shipping_text_3')}}</p>
      <p><strong>{{__('text.shipping_title2')}}</strong></p>
      <div class="raw-content__section-mt">
        <p>{{__('text.shipping_text_4')}}</p>
        <p>{{__('text.shipping_text_5')}}</p>
      </div>
      <div class="raw-content__section-mt">
        <ul>
          <li>{{__('text.shipping_text_6')}}</li>
          <li>{{__('text.shipping_text_7')}}</li>
          <li>{{__('text.shipping_text_8')}}</li>
          <li>{{__('text.shipping_text_9')}}</li>
        </ul>
      </div>
      <div class="raw-content__section-mt">
        <p>{{__('text.shipping_text_10')}}<a href="{{ route('home.contact_us') }}">{{__('text.shipping_contact_us_shipping')}}</a>
        </p>
      </div>
    </article>
  </main>
  @endsection