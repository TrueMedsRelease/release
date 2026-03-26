
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
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#visa') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/visa.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#mastercard') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/mastercard.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#maestro') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/maestro.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#discover') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/discover.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amex') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/amex.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#jsb') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/jsb.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' union-pay' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#unionpay') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/unionpay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' unionpay' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#dinners-club') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/dinners-club.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#apple-pay') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/apple-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#google-pay') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/google-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amazon-pay') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/amazon-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#stripe') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/stripe.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#paypal') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/paypal.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#sepa') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/sepa.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#cashapp') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/cashapp.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#adyen') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/adyen.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#skrill') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/skrill.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#worldpay') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/worldpay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#payline') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/payline.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#bitcoin') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/bitcoin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#binance-coin') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/binance-coin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#ethereum') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/ethereum.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#litecoin') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/litecoin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#tron') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/tron.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(erc20)') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/usdt(erc20).svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                    @endif
                </li>
                <li class="pay-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(trc20)') }}">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/usdt(trc20).svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                    @endif
                </li>
            </ul>
        </div>
    </section>

    <div class="checkup img__container" onclick="location.href='{{ route('home.checkup') }}'">
        <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_big.png") }}">
    </div>

    <input type="hidden" id="error_subject" value="{{ $error_subject }}">
    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{__('text.contact_us_title')}}</h1>
                <div class="message_sended hidden">
                    <h2>{{__('text.contact_us_thanks')}}</h2>
                    <br>
                    <p>{{__('text.contact_us_sended')}}</p>
                </div>
                <form class="form" id = "message_send_form" method="post">
                    <div class="form__body">
                        <div class="form__inner">
                            <div class="form__default-rows">
                                <div class="form__row">
                                    <label for="subject" class="form__label">{{__('text.contact_us_subject')}}</label>
                                    <div class="form__field custom-field" id="contact-subject" name="contact-subject">
                                        <div id="subject_block">
                                            <div class="contact_subject">
                                                <div id="new_subject_block">
                                                    <div class="select_subject">
                                                        <div class="select_header_subject">
                                                            <span class="select_current_subject" curr_subject_id = "{{ $default_subject }}">{{ $subjects[$default_subject] }}</span>
                                                            <div class="select_icon">
                                                                <img src="{{ asset("$design/images/icons/arrow_down_black.svg") }}">
                                                            </div>
                                                        </div>
                                                        <div class="select_body_subjects">
                                                            @foreach ($subjects as $id => $subject)
                                                                <div class="select_item_subject" subject_id = "{{ $id }}">{{ $subject }}</div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form__row">
                                    <label for="name" class="form__label">{{__('text.contact_us_name')}}</label>
                                    <input data-required id = "name" autocomplete="off" type="text" name="form[name]" data-error="" placeholder="{{__('text.contact_us_name')}}" class="form__input input">
                                </div>
                                <div class="form__row">
                                    <label for="email_form" class="form__label">{{__('text.contact_us_email')}}</label>
                                    <input data-required id="email_form" autocomplete="off" type="text" name="form[email_form]" data-error="" placeholder="{{__('text.contact_us_email')}}" class="form__input input">
                                </div>
                                <div class="form__row form__row--top-alignment">
                                    <label for="message" class="form__label">{{__('text.contact_us_message')}}</label>
                                    <textarea autocomplete="off" id = "message" type="text" name="form[message]" data-error="" placeholder="{{__('text.contact_us_message')}}" class="form__input input"></textarea>
                                </div>
                            </div>
                            <div class="form__row form__row--captcha">
                                <label for="name" class="form__label">{{__('text.contact_us_code')}}</label>
                                <div class="form__input">
                                    <picture>
                                        <img loading="lazy" id="captcha_image" src="{{ captcha_src() }}" style="border-radius: 0.625rem; margin-bottom: 5px;">
                                    </picture>
                                    <input autocomplete="off" type="text" id = "captcha" name="form[captcha]" data-error="" placeholder="{{__('text.contact_us_code')}}" class="form__input input">
                                </div>
                            </div>
                        </div>
                        <div class="form__desrc">
                            <div class="contact_us_phones form__text-block">
                                <p>{{__('text.phones_title_support')}}</p>
                                <a href="tel:{{__('text.phones_title_phone_1')}}">{{__('text.phones_title_phone_1')}}</a>
                                <br>
                                <a href="tel:{{__('text.phones_title_phone_1')}}">{{__('text.phones_title_phone_2')}}</a>
                            </div>
                            <div class="form__text-block">
                                <p>{{__('text.contact_us_describe1')}}</p>
                            </div>
                            <div class="form__text-block">
                                <p>{{__('text.contact_us_describe2')}}</p>
                            </div>
                            <div class="form__text-block">
                                <p>{{__('text.contact_us_describe3')}}</p>
                            </div>
                        </div>
                    </div>
                    <input onclick="sendAjaxContact()" type="button" name="form[submit]" value="{{__('text.contact_us_send')}}"  id = "message_send_button" class="form__button"></input>
                </form>
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
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg width="20" height="20">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-prev") }}"></use>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20">
                            <g clip-path="url(#arrPrevClip)">
                                <path d="M.733 11.742 3.958 15a.833.833 0 0 0 1.184 0 .833.833 0 0 0 0-1.183l-2.967-2.984h16.992a.833.833 0 1 0 0-1.666H2.125L5.142 6.15a.833.833 0 0 0 0-1.15.833.833 0 0 0-1.184 0L.733 8.208a2.5 2.5 0 0 0 0 3.534Z"/>
                            </g>
                            <defs>
                                <clipPath id="arrPrevClip">
                                    <path d="M0 0h20v20H0z"/>
                                </clipPath>
                            </defs>
                        </svg>
                    @endif
                    <span>{{__('text.testimonials_prev')}}</span>
                </button>
                <button type="button" class="reviews__arrow reviews__arrow--next">
                    <span>{{__('text.testimonials_next')}}</span>
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg width="20" height="20">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-next") }}"></use>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20">
                            <g clip-path="url(#arrNextClip)">
                                <path d="M19.267 8.258 16.04 5a.833.833 0 0 0-1.183 0 .833.833 0 0 0 0 1.183l2.967 2.984H.833a.833.833 0 0 0 0 1.666h17.042l-3.017 3.017a.834.834 0 0 0 0 1.15.834.834 0 0 0 1.183 0l3.226-3.208a2.5 2.5 0 0 0 0-3.534Z"/>
                            </g>
                            <defs>
                                <clipPath id="arrNextClip">
                                    <path d="M0 0h20v20H0z" transform="rotate(-180 10 10)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    @endif
                </button>
            </div>
        </div>
    </div>
</section>

@endsection