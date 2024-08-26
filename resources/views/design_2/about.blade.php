
@extends($design . '.layouts.main')

@section('title', 'About')

@section('content')
<main class="default">
    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{__('text.about_us_title')}}</h1>
                <div class="default__text">
                    {!!__('text.about_us_text')!!}
                <h2 style="font-weight: bold;">{{__('text.about_us_title1')}}</h2>
                <p>{{__('text.about_us_text1')}}</p>
                <h2 style="font-weight: bold;">{{__('text.about_us_title2')}}</h2>
                <ul>{{__('text.about_us_text2_1')}}</ul>
                    <li>{{__('text.about_us_text2_2')}}</li>
                    <li>{{__('text.about_us_text2_3')}}</li>
                    <p></p>
                <ul>{{__('text.about_us_text2_4')}}</ul>
                    <li>{{__('text.about_us_text2_5')}}</li>
                    <li>{{__('text.about_us_text2_6')}}</li>
                    <p></p>
                <h2 style="font-weight: bold;">{{__('text.about_us_title3')}}</h2>
                <p>{{__('text.about_us_text3_1')}}</p>
                <p>{{__('text.about_us_text3_2')}}</p>
                <ul>{{__('text.about_us_text3_3')}}</ul>
                    <li>{{__('text.about_us_text3_4')}}</li>
                    <li>{{__('text.about_us_text3_5')}}</li>
                    <p></p>
                <p>{{__('text.about_us_text3_6')}}</p>
                <h2 style="font-weight: bold;">{{__('text.about_us_title4')}}</h2>
                <p>{{__('text.about_us_text4')}}</p>
                </div>
            </div>
            <aside class="default__aside">
                <div class="default__offers">
                    <a href="#" class="default__item-offer">
                        <picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/01.jpg") }}" alt=""></picture>
                    </a>
                    <a href="#" class="default__item-offer">
                        <picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/02.jpg") }}" alt=""></picture>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</main>


@endsection