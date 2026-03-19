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

    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{__('text.testimonials_title')}}</h1>
                <div class="default__desrc-reviews">
                    <p>{!! __('text.testimonials_text') !!}</p>
                </div>
                <div class="default__reviews">
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_1')}} {!!__('text.testimonials_author_t_1')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_2')}} {!!__('text.testimonials_author_t_2')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_3')}} {!!__('text.testimonials_author_t_3')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_4')}} {!!__('text.testimonials_author_t_4')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_5')}} {!!__('text.testimonials_author_t_5')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_6')}} {!!__('text.testimonials_author_t_6')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_7')}} {!!__('text.testimonials_author_t_7')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_8')}} {!!__('text.testimonials_author_t_8')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_9')}} {!!__('text.testimonials_author_t_9')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_10')}} {!!__('text.testimonials_author_t_10')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_11')}} {!!__('text.testimonials_author_t_11')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_12')}} {!!__('text.testimonials_author_t_12')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_13')}} {!!__('text.testimonials_author_t_13')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_14')}} {!!__('text.testimonials_author_t_14')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_15')}} {!!__('text.testimonials_author_t_15')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_16')}} {!!__('text.testimonials_author_t_16')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_17')}} {!!__('text.testimonials_author_t_17')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_18')}} {!!__('text.testimonials_author_t_18')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_19')}} {!!__('text.testimonials_author_t_19')!!}</p>
                    </div>
                    <div class="default__review">
                        <p>{{__('text.testimonials_t_20')}} {!!__('text.testimonials_author_t_20')!!}</p>
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
            </aside>
        </div>
    </div>
</main>

@endsection