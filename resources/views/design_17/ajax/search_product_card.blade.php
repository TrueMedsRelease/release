@php
    $activeNames = [];
    foreach (($product['aktiv'] ?? []) as $aktiv) {
        if (!empty($aktiv['name'])) {
            $activeNames[] = $aktiv['name'];
        }
    }

    $is_catalog = $is_catalog ?? false;
    $productUrl = route('home.product', $product['url']);
@endphp

@if ($is_catalog)
    <a class="card-link chat-search-card-link" href="{{ $productUrl }}" data-product-title="{{ $product['name'] }}">
@endif
    <article class="card chat-search-card">
    <div class="card__header">
        <h2 class="card__title">
            @if ($is_catalog)
                <span>{{ $product['name'] }}</span>
            @else
                <a class="js-chat-product-link" href="{{ $productUrl }}" data-product-title="{{ $product['name'] }}">{{ $product['name'] }}</a>
            @endif
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
            <span class="card__price">{{ $Currency::convert($product['price'], false, true) }}</span>
        </div>
        @if ($is_catalog)
            <span class="card__button button button--outlined">
                {{ __('text.product_add_to_cart_text') }}
            </span>
        @else
            <a class="card__button button button--outlined js-chat-product-link" href="{{ $productUrl }}" data-product-title="{{ $product['name'] }}">
                {{ __('text.product_add_to_cart_text') }}
            </a>
        @endif
    </div>
    </article>
@if ($is_catalog)
    </a>
@endif
