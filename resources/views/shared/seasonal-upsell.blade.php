@if(config('seasonal_upsell.enabled'))
@php
    if (!function_exists('asset_ver')) {
        function asset_ver(string $path): string {
            static $mtimes = [];
            $full = public_path($path);
            if (!isset($mtimes[$path])) {
                $mtimes[$path] = is_file($full) ? filemtime($full) : null;
            }
            $url = asset($path);
            $v = $mtimes[$path] ?? time();
            return $url . '?v=' . $v;
        }
    }
@endphp
@php
    $upsellButtons = [
        'design_1' => ['order__continue', 'order__pay'],
        'design_2' => ['actions-line__continue', 'actions-line__pay'],
        'design_3' => ['actions-line__continue', 'actions-line__pay'],
        'design_4' => ['button button--stroke', 'button button--filled'],
        'design_5' => ['btn btn-default blue', 'btn btn-primary'],
        'design_7' => ['actions-line__continue button button--outline', 'actions-line__pay button button--outline'],
        'design_8' => ['actions-line__continue button button--outline', 'actions-line__pay button button--outline'],
        'design_9' => ['actions-line__continue', 'actions-line__pay'],
        'design_10' => ['button button--outline', 'button'],
        'design_11' => ['button button--uppercase', 'button button--uppercase'],
        'design_12' => ['button button--uppercase', 'button button--uppercase'],
        'design_14' => ['button button--uppercase', 'button button--uppercase'],
        'design_15' => ['button button--secondary', 'button'],
        'design_16' => ['button button--dark-surface', 'button'],
        'design_17' => ['button button--white', 'button button--white--border'],
    ][$design ?? ''] ?? ['su-fallback-back', 'su-fallback-continue'];
@endphp
<link rel="stylesheet" href="{{ asset_ver('shared/seasonal-upsell.css') }}">
<dialog id="seasonal-upsell" aria-labelledby="seasonal-upsell-title" data-design="{{ $design ?? '' }}"
    data-api="{{ route('seasonal-upsell.index') }}"
    data-add="{{ route('seasonal-upsell.add') }}"
    data-checkout="{{ route('checkout.index') }}"
    data-csrf="{{ csrf_token() }}"
    data-price-prefix="{{ __('text.cart_only') }}"
    data-error="{{ __('text.upsell_error') }}"
    data-pack-label="{{ __('text.checkout_package') }}"
    data-quantity-label="{{ __('text.checkout_qty') }}"
    data-select-label="{{ __('text.common_add_to_cart_text_d2') }}">
    <div class="su-body">
        <h2 id="seasonal-upsell-title">{{ __('text.upsell_title') }}</h2>
        <div class="su-items"></div>
        <p class="su-error" role="alert" hidden></p>
        <div class="su-actions">
            <button type="button" class="su-back {{ $upsellButtons[0] }}"><svg class="su-arrow" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M20 12H4 M10 6l-6 6 6 6"/></svg><span>{{ __('text.cart_back_to_shop') }}</span></button>
            <button type="button" class="su-continue {{ $upsellButtons[1] }}"><span>{{ __('text.cart_pay_button') }}</span><svg class="su-arrow" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M4 12h16 M14 6l6 6-6 6"/></svg></button>
        </div>
        {{-- <a class="su-skip" hidden href="{{ route('checkout.index') }}">{{ __('seasonal_upsell.skip') }}</a> --}}
    </div>
</dialog>
<script src="{{ asset_ver('shared/seasonal-upsell.js') }}" defer></script>
@endif
