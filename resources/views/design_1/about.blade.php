
@extends($design . '.layouts.main')

@section('title', 'About')

@section('content')
<main class="main">
    <article class="raw-content raw-content--narrow">
      <h1>{{__('text.about_us_title')}}</h1>
      <p>{!!__('text.about_us_text')!!}</p>
      <h2>{{__('text.about_us_title1')}}</h2>
      <p>{{__('text.about_us_text1')}}</p>
      <h2>{{__('text.about_us_title2')}}</h2>
      <div class="raw-content__lists">
        <p>{{__('text.about_us_text2_1')}}</p>
        <ul>
          <li>{{__('text.about_us_text2_2')}}</li>
          <li>{{__('text.about_us_text2_3')}}</li>
        </ul>
        <p>{{__('text.about_us_text2_4')}}</p>
        <ul>
          <li>{{__('text.about_us_text2_5')}}</li>
          <li>{{__('text.about_us_text2_6')}}</li>
        </ul>
      </div>
      <h2>{{__('text.about_us_title3')}}</h2>
      <p class="default-paragraph">{{__('text.about_us_text3_1')}}</p>
      <p>{{__('text.about_us_text3_2')}}</p>
      <p>{{__('text.about_us_text3_3')}}</p>
      <ul>
        <li>{{__('text.about_us_text3_4')}}</li>
        <li>{{__('text.about_us_text3_5')}}</li>
      </ul>
      <p>{{__('text.about_us_text3_6')}}</p>
      <h2>{{__('text.about_us_title4')}}</h2>
      <p>{{__('text.about_us_text4')}}</p>
    </article>
  </main>
  @endsection