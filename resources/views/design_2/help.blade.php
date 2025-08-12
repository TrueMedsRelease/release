@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')

<main class="default">
    <section class="pay-index">
        <div class="pay-index__container">
            <ul class="pay-index__list">
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#visa') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#mastercard') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#maestro') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#discover') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amex') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#jsb') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' union-pay' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#unionpay') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#dinners-club') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#apple-pay') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#google-pay') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amazon-pay') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#stripe') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#paypal') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#sepa') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#cashapp') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#adyen') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#skrill') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#worldpay') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#payline') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#bitcoin') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#binance-coin') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#ethereum') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#litecoin') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#tron') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(erc20)') }}">
                    </svg>
                </li>
                <li class="pay-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(trc20)') }}">
                    </svg>
                </li>
            </ul>
        </div>
    </section>

    <div class="christmas img__container" style="display: none"  onclick="location.href='{{ route('home.checkup') }}'">
        <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_big.png") }}">
    </div>

    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{__('text.faq_faq_title')}}</h1>
                <ul class="default__list">
                    <li class="default__item-list q_1">{{__('text.faq_q_1')}}</li>
                    <li class="default__item-list q_2">{{__('text.faq_q_2')}}</li>
                    <li class="default__item-list q_3">{{__('text.faq_q_3')}}</li>
                    <li class="default__item-list q_4">{{__('text.faq_q_4')}}</li>
                    <li class="default__item-list q_5">{{__('text.faq_q_5')}}</li>
                    <li class="default__item-list q_6">{{__('text.faq_q_6')}}</li>
                    <li class="default__item-list q_7">{{__('text.faq_q_7')}}</li>
                    <li class="default__item-list q_8">{{__('text.faq_q_8')}}</li>
                    <li class="default__item-list q_9">{{__('text.faq_q_9')}}</li>
                    <li class="default__item-list q_10">{{__('text.faq_q_10')}}</li>
                    <li class="default__item-list q_11">{{__('text.faq_q_11')}}</li>
                    <li class="default__item-list q_12">{{__('text.faq_q_12')}}</li>
                    <li class="default__item-list q_13">{{__('text.faq_q_13')}}</li>
                    <li class="default__item-list q_14">{{__('text.faq_q_14')}}</li>
                    <li class="default__item-list q_15">{{__('text.faq_q_15')}}</li>
                    <li class="default__item-list q_16">{{__('text.faq_q_16')}}</li>
                    <li class="default__item-list q_17">{{__('text.faq_q_17')}}</li>
                    <li class="default__item-list q_18">{{__('text.faq_q_18')}}</li>
                    <li class="default__item-list q_19">{{__('text.faq_q_19')}}</li>
                    <li class="default__item-list q_20">{{__('text.faq_q_20')}}</li>
                    <li class="default__item-list q_21">{{__('text.faq_q_21')}}</li>
                    <li class="default__item-list q_22">{{__('text.faq_q_22')}}</li>
                    <li class="default__item-list q_23">{{__('text.faq_q_23')}}</li>
                    <li class="default__item-list q_24">{{__('text.faq_q_24')}}</li>
                    <li class="default__item-list q_25">{{__('text.faq_q_25')}}</li>
                    <li class="default__item-list q_26">{{__('text.faq_q_26')}}</li>
                    <li class="default__item-list q_27">{{__('text.faq_q_27')}}</li>
                    <li class="default__item-list q_28">{{__('text.faq_q_28')}}</li>
                    <li class="default__item-list q_29">{{__('text.faq_q_29')}}</li>
                    <li class="default__item-list q_30">{{__('text.faq_q_30')}}</li>
                    <li class="default__item-list q_31">{{__('text.faq_q_31')}}</li>
                </ul>
                <div class="default__answers">
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_1">{{__('text.faq_q_1')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_1')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_2">{{__('text.faq_q_2')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_2')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_3">{{__('text.faq_q_3')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_3')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_4">{{__('text.faq_q_4')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_4')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_5">{{__('text.faq_q_5')}}</h3>
                        <div class="default__text">
                        <p>{{__('text.faq_a_5_1')}}</p>
                        <p>{{__('text.faq_a_5_2')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_6">{{__('text.faq_q_6')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_6')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_7">{{__('text.faq_q_7')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_7')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_8">{{__('text.faq_q_8')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_8')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_9">{{__('text.faq_q_9')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_9')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_10">{{__('text.faq_q_10')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_10')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_11">{{__('text.faq_q_11')}}</h3>
                        <div class="default__text">
                        <p>{{__('text.faq_a_11_1')}}</p>
                        <p>{{__('text.faq_a_11_2')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_12">{{__('text.faq_q_12')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_12')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_13">{{__('text.faq_q_13')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_13')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_14">{{__('text.faq_q_14')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_14')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_15">{{__('text.faq_q_15')}}</h3>
                        <div class="default__text">
                        <p>{{__('text.faq_a_15_1')}}</p>
                        <p>{{__('text.faq_a_15_2')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_16">{{__('text.faq_q_16')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_16')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_17">{{__('text.faq_q_17')}}</h3>
                        <div class="default__text">
                        <p>{{__('text.faq_a_17_1')}}</p>
                        <p>{{__('text.faq_a_17_2')}}</p>
                        <p>{{__('text.faq_a_17_3')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_18">{{__('text.faq_q_18')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_18')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_19">{{__('text.faq_q_19')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_19')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_20">{{__('text.faq_q_20')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_20')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_21">{{__('text.faq_q_21')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_21')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_22">{{__('text.faq_q_22')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_22')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_23">{{__('text.faq_q_23')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_23')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_24">{{__('text.faq_q_24')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_24')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_25">{{__('text.faq_q_25')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_25')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_26">{{__('text.faq_q_26')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_26')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_27">{{__('text.faq_q_27')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_27')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_28">{{__('text.faq_q_28')}}</h3>
                        <div class="default__text">
                        <p>{{__('text.faq_a_28_1')}}</p>
                        <p>{{__('text.faq_a_28_2')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_29">{{__('text.faq_q_29')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_29')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_30">{{__('text.faq_q_30')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_30')}}</p>
                        </div>
                    </div>
                    <div class="default__answer">
                        <h3 class="default__caption" id = "q_31">{{__('text.faq_q_31')}}</h3>
                        <div class="default__text">
                            <p>{{__('text.faq_a_31')}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <aside class="default__aside">
                <div class="default__offers">
                    <a href="#" class="default__item-offer">
                        <picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/offers/01.jpg") }}" alt=""></picture>
                    </a>
                    <a href="#" class="default__item-offer">
                        <picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/offers/02.jpg") }}" alt=""></picture>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</main>

@endsection

@section('reviews')

<section class="reviews">
    <div class="reviews__container">
        <div class="reviews__body">
            <div class="reviews__slider">
                <div class="reviews__swiper">
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_1')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_1')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_2')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_2')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_3')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_3')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_4')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_4')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_5')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_5')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_6')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_6')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_7')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_7')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_8')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_8')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_9')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_9')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_10')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_10')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_11')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_11')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_12')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_12')}}</div>
                    </div>
                </div>
            </div>
            <div class="reviews__controls">
                <button type="button" class="reviews__arrow reviews__arrow--prev">
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-prev") }}"></use>
                    </svg>
                    <span>{{__('text.testimonials_prev')}}</span>
                </button>
                <button type="button" class="reviews__arrow reviews__arrow--next">
                    <span>{{__('text.testimonials_next')}}</span>
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-next") }}"></use>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>

@endsection