{{--
    Price with the coupon applied, under a package price on the product page: [−10%] $108.
    @include('shared.coupon_price', ['couponBase' => $item['price'], 'couponProductId' => $product['id']])
    No <span>/<div> inside on purpose: some designs restyle every span in the price cell (design_5 mobile).
--}}
@php $cpDiscount = \App\Services\CouponService::discount((float) $couponBase, $couponProductId ?? null); @endphp
@if ($cpDiscount > 0)
    @include('shared.coupon_price_css')
    <small class="coupon-price" title="{{ __('text.coupon_cart_note') }}">@include('shared.coupon_tag') <em class="coupon-price__label">{{ __('text.coupon_price_label') }}</em> <b class="coupon-price__sum">{{ \App\Models\Currency::convert($couponBase - $cpDiscount, true) }}</b></small>
@endif
