@php $crDiscount = \App\Services\CouponService::discount((float) $couponBase, $couponProductId ?? null); @endphp
@if ($crDiscount > 0)
    @include('shared.coupon_tag', ['css' => true])
    @once
        <style>
            /* the package row and its coupon row read as one block: no line between them,
               and the coupon cell is not the table's left edge (the package cell spans both rows) */
            tr.product--has-coupon > td:not([rowspan]) { border-bottom: 0 !important; padding-bottom: 4px; }
            tr.product--coupon > td { padding-top: 0; border-top: 0 !important; border-left: 0 !important; border-right: 0 !important; vertical-align: middle; }
            .coupon-row {
                display: flex; align-items: center; gap: 10px;
                margin: 0 0 6px; padding: 7px 12px; border-radius: 10px;
                background: rgba(46, 158, 79, .08);
                background: color-mix(in srgb, var(--coupon-color, #2e9e4f) 10%, transparent);
                font-size: 14px; line-height: 1.3; color: inherit; text-align: start;
            }
            .coupon-row__label { flex: 1 1 auto; min-width: 0; opacity: .75; font-style: normal; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .coupon-row__sum { flex: none; font-size: 16px; font-weight: 700; white-space: nowrap; }
            .coupon-row .coupon-tag { flex: none; font-size: 12px; padding: 3px 7px 3px 6px; }
            /* phones: the tag already says "coupon −10%"; keep tag + price on one line */
            @media (max-width: 480px) {
                .coupon-row { justify-content: space-between; gap: 6px; padding: 6px 8px; }
                .coupon-row__label { display: none; }
                .coupon-row__sum { font-size: 15px; }
            }
        </style>
    @endonce
    <tr class="product product--coupon">
        <td colspan="{{ $couponColspan ?? 2 }}">
            <div class="coupon-row" title="{{ __('text.coupon_cart_note') }}">
                @include('shared.coupon_tag')
                <em class="coupon-row__label">{{ __('text.coupon_price_label') }}</em>
                <b class="coupon-row__sum">{{ \App\Models\Currency::convert($couponBase - $crDiscount, true) }}</b>
            </div>
        </td>
    </tr>
@endif
