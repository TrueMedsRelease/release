@php
    $activeNames = [];
    foreach (($product['aktiv'] ?? []) as $aktiv) {
        if (!empty($aktiv['name'])) {
            $activeNames[] = $aktiv['name'];
        }
    }
@endphp

<article class="card chat-search-card">
    <div class="card__header">
        <h2 class="card__title">
            <a class="js-chat-product-link" href="{{ route('home.product', $product['url']) }}" data-product-title="{{ $product['name'] }}">{{ $product['name'] }}</a>
        </h2>
        @if (!empty($activeNames))
            <div class="card__description">
                {{ implode(', ', $activeNames) }}
            </div>
        @endif
    </div>

    <div class="card__img">
        @if ($product['id'] == 616)
            <picture>
                <source type="image/webp" srcset="{{ asset("$design/img/products/gift-125w.webp") }} 1x, {{ asset("$design/img/products/gift-251w.webp") }} 2x">
                <img src="{{ asset("$design/img/products/gift-125w.jpg") }}"
                    srcset="{{ asset("$design/img/products/gift-125w.jpg") }} 1x, {{ asset("$design/img/products/gift-251w.jpg") }} 2x"
                    width="126" height="126" alt="{{ $product['image'] }}">
            </picture>
        @else
            <picture style="max-height: 126px; max-width: 126px;">
                <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="max-height: 126px; max-width: 126px;">
            </picture>
        @endif
    </div>

    @if ($product['id'] != 616 && !in_array($product['id'], [619, 620, 483, 484, 501, 615]) && !empty($product['product_dosages']))
        <div class="card__variants">
            @foreach ($product['product_dosages'] as $dosage)
                <span class="card__variant">{{ $dosage }}</span>
            @endforeach
        </div>
    @endif

    <div class="card__footer">
        <div class="card__price-wrapper">
            @if ($product['id'] != 616)
                <span class="card__price">{{ $Currency::convert($product['price'], false, true) }} {{ strtolower(__('text.common_per_pill')) }}</span>
            @else
                <span class="card__price">{{ $Currency::convert($product['price'], false, true) }}</span>
            @endif
        </div>
        <a class="card__button button button--outlined js-chat-product-link" href="{{ route('home.product', $product['url']) }}" data-product-title="{{ $product['name'] }}">
            {{ __('text.product_add_to_cart_text') }}
        </a>
    </div>
</article>
