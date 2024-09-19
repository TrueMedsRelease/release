
@extends($design . '.layouts.main')

@section('title', $title)

@section('content')
<main class="page-text">
    <div class="page-text__container">
        <div class="page-text__body">
            <div class="page-text__content">
                <div class="page-text__top-row">
                    <h1 class="page-text__title title">{{__('text.shipping_title')}}</h1>
                </div>
                <div class="page-text__inner">
                    <div class="page-text__block shipping-block">
                        <p class="shipping-block__text"><b>{{__('text.shipping_title1')}}</b></p>
                        <div class="shipping-block__items">
                            <div class="shipping-block__item">
                                <div class="shipping-block__icon">
                                    <img src="{{ asset("$design/images/icons/f-01.svg") }}" width="40" height="40" alt="Awesome image">
                                </div>
                                <div class="shipping-block__info-item">
                                    <p>{!!__('text.shipping_text_1')!!}</p>
                                </div>
                            </div>
                            <div class="shipping-block__item">
                                <div class="shipping-block__icon">
                                    <img src="{{ asset("$design/images/icons/f-02.svg") }}" width="40" height="40" alt="Awesome image">
                                </div>
                                <div class="shipping-block__info-item">
                                    <p>{!!__('text.shipping_text_2')!!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-text__block">
                        <p>{{__('text.shipping_text_3')}}</p>
                    </div>

                    <div class="page-text__block">
                        <p>{{__('text.shipping_title2')}}</p>
                    </div>

                    <div class="page-text__block">
                        <p>{{__('text.shipping_text_4')}}</p>
                    </div>

                    <div class="page-text__block">
                        <p>{{__('text.shipping_text_5')}}</p>
                    </div>

                    <div class="page-text__block">
                        <div class="page-text__list">
                            <li>{{__('text.shipping_text_6')}}</li>
                            <li>{{__('text.shipping_text_7')}}</li>
                            <li>{{__('text.shipping_text_8')}}</li>
                            <li>{{__('text.shipping_text_9')}}</li>
                        </div>
                    </div>

                    <div class="page-text__block">
                        <p>{{__('text.shipping_text_10')}}<a href="{{ route('home.contact_us') }}">{{__('text.shipping_contact_us_shipping')}}</a></p>
                    </div>

                </div>
            </div>
            <aside class="page-text__sidebar">
                <div class="page-text__offers">
                    <div class="page-text__offer">
                        <picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/01.png") }}" alt=""></picture>
                    </div>
                    <div class="page-text__offer">
                        <picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/02.png") }}" alt=""></picture>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</main>

@endsection