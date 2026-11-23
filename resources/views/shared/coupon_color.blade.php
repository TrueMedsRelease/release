{{--
    --coupon-color: each design's brand colour = its main button colour (its CSS variables,
    so a theme recolour follows automatically). Printed once per page by coupon_tag / coupon_bar.
--}}
@once
@php
    $ccDesign = $design ?? session('design', config('app.design'));
    $ccColor = [
        'design_1'  => 'var(--green, #477215)',
        'design_2'  => 'var(--accent-color, #28d17c)',
        'design_3'  => 'var(--yellow, #f5e175)',
        'design_4'  => 'var(--primary, #54a8c2)',
        'design_5'  => 'var(--primary, #54a8c2)',
        'design_7'  => 'var(--orange, #ff8200)',
        'design_8'  => 'var(--blue-main, #087ed8)',
        'design_9'  => 'var(--accent-color, #4494de)',
        'design_10' => 'var(--color-primary, #2a464d)',
        'design_11' => 'var(--color-primary, #616ede)',
        'design_12' => 'var(--color-primary, #c9b09a)',
        'design_14' => 'var(--color-primary, #28d17c)',
        'design_15' => 'var(--color-primary, #474ce6)',
        'design_16' => 'var(--color-primary, #bcbcbc)',
        'design_17' => 'var(--color-primary, #13163f)',
    ][$ccDesign] ?? '#2e9e4f';

    $ccLight = [
        'design_3'  => 'var(--black, #000)',          // yellow buttons, dark text
        'design_14' => 'var(--color-white, #fff)',    // green buttons, white text
    ][$ccDesign] ?? null;
@endphp
<style>:root { --coupon-color: {{ $ccColor }};@if ($ccLight) --coupon-bg: {{ $ccColor }}; --coupon-on: {{ $ccLight }};@endif }</style>
@endonce
