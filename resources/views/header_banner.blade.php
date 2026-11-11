@php
    $headerBanner = config('banner.header');
@endphp
@if ($headerBanner && in_array($headerBanner, config('banner.available', []), true))
    <div class="christmas" style="display: none"
         data-banner="{{ $headerBanner }}"
         @foreach (['biggest', 'big', 'middle', 'small'] as $bannerSize)
             data-src-{{ $bannerSize }}="{{ asset('pub_images/' . $headerBanner . '_' . $bannerSize . '.png') }}"
         @endforeach
    >
        <img src="{{ asset('pub_images/' . $headerBanner . '_big.png') }}" alt="">
    </div>
@endif
