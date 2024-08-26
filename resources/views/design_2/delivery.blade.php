
@extends($design . '.layouts.main')

@section('title', 'Delivery')

@section('content')
<main class="default">
    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{__('text.shipping_title')}}</h1>
                <div class="default__text">
                <h3 style="font-weight: bold;">{{__('text.shipping_title1')}}</h3>
                <br>
                <li>{!!__('text.shipping_text_1')!!}</li>
                <li>{!!__('text.shipping_text_2')!!}</li>
                <p></p>
                <p>{{__('text.shipping_text_3')}}</p>
                <h3 style="font-weight: bold;">{{__('text.shipping_title2')}}</h3>
                <br>
                <p>{{__('text.shipping_text_4')}}</p>
                <p>{{__('text.shipping_text_5')}}</p>
                <li>{{__('text.shipping_text_6')}}</li>
                <li>{{__('text.shipping_text_7')}}</li>
                <li>{{__('text.shipping_text_8')}}</li>
                <li>{{__('text.shipping_text_9')}}</li>
                <p></p>
                <p>{{__('text.shipping_text_10')}}<a class="order__upgrade" style="font-size: 16px" href="{{ route('home.contact_us') }}">{{__('text.shipping_contact_us_shipping')}}</a></p>
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