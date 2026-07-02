@foreach (session('cart', []) as $product)
    <article class="cart-item">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a class="cart-item__cover" href="{{ route('home.product', $product['product_info']['url']) }}">
                @if ($product['product_id'] == 616)
                    <picture>
                        <source type="image/webp"
                            srcset="{{ asset("$design/img/products/gift-product-175w.webp") }} 1x, {{ asset("$design/img/products/gift-product-350w.webp 2x") }}"><img
                            src="{{ asset("$design/img/products/gift-product-175w.jpg") }}"
                            srcset="{{ asset("$design/img/products/gift-product-175w.jpg") }} 1x, {{ asset("$design/img/products/gift-product-350w.jpg 2x") }}" width="200"
                            height="200" alt="Gift">
                    </picture>
                @else
                    <picture>
                        <source srcset="{{ route('home.set_images', $product['product_info']['image']) }}" type="image/webp">
                        <img loading="lazy" src="{{ route('home.set_images', $product['product_info']['image']) }}" alt="{{ $product['product_info']['alt'] }}">
                    </picture>
                @endif
            </a>
            <div class="cart-item__content">
                <h3 class="cart-item__title truncate-box">
                    <a href="{{ route('home.product', $product['product_info']['url']) }}">{{ $product['product_info']['name'] }}</a>
                </h3>
                <p class="cart-item__description truncate-box">{{ $product['dosage'] }} x {{ $product['num'] }}</p>
            </div>
        </div>
        <div class="cart-item__price">{{ $product['q'] }} x {{ $Currency::convert($product['price'], true) }}</div>
        <div class="cart-item__price">{{ $Currency::convert($product['q'] * $product['price'], true) }}</div>
        <button class="cart-item__remove-button js-cart-remove-button" type="button" data-cart-remove-pack="{{ $product['pack_id'] }}">
            <span class="icon">
                <svg width="1em" height="1em" fill="currentColor">
                    <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#trash-round') }}"></use>
                </svg>
            </span>
        </button>
    </article>
@endforeach
