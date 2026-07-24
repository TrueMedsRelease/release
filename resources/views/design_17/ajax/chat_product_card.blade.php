@if (!empty($products))
<div class="product-cards">
    <div class="cards">
        @foreach ($products as $index => $product)
            @php
                $packs = $product['packs'] ?? [];
                $productName = $product['name'] ?? $product['id'] ?? '';
                $variantDosages = [];
                $maxDiscount = 0;
                $minPack = null;
                foreach ($packs as $pack) {
                    if (!empty($pack['dosage']) && !in_array($pack['dosage'], $variantDosages)) {
                        $variantDosages[] = $pack['dosage'];
                    }
                    if (!empty($pack['old_price']) && $pack['old_price'] > $pack['price']) {
                        $d = round((1 - $pack['price'] / $pack['old_price']) * 100);
                        if ($d > $maxDiscount) $maxDiscount = $d;
                    }
                    if ($minPack === null || $pack['price'] < $minPack['price']) {
                        $minPack = $pack;
                    }
                }
                $perPillPrice = 0;
                if ($minPack && !empty($minPack['quantity']) && $minPack['quantity'] > 0) {
                    $perPillPrice = round($minPack['price'] / $minPack['quantity'], 2);
                }
                $productType = $product['product_type'] ?? '';
                $productSlug = $product['slug'] ?? '';
                $productUrl = $productSlug ? route('home.product', $productSlug) : '#';
            @endphp
            <a class="card-link" href="{{ $productUrl }}" data-product-index="{{ $index }}">
                <article class="card">
                    @if (!empty($product['image']))
                        <div class="card__img">
                            <picture>
                                <source srcset="{{ $product['image'] }}" type="image/webp">
                                <img src="{{ $product['image'] }}" alt="{{ $productName }}" loading="lazy">
                            </picture>
                        </div>
                    @endif
                    <div class="card__header">
                        <h2 class="card__title">
                            <span>{{ $productName }}</span>
                        </h2>
                        {{-- @if (!empty($product['dosage']))
                            <div class="card__description">{{ $product['dosage'] }}</div>
                        @endif --}}
                        @if (!empty($variantDosages))
                        <div class="card__variants">
                            @foreach ($variantDosages as $dosage)
                                <div class="card__variant">{{ $dosage }}</div>
                            @endforeach
                        </div>
                    @endif
                    </div>
                    {{-- @if (!empty($variantDosages))
                        <div class="card__variants">
                            @foreach ($variantDosages as $dosage)
                                <div class="card__variant">{{ $dosage }}</div>
                            @endforeach
                        </div>
                    @endif --}}
                    <div class="card__footer">
                        <div class="card__price-wrapper">
                            <span class="card__price">
                                @if ($perPillPrice > 0)
                                    @php $unitLabel = $productType ? strtolower($productType) : 'pill'; @endphp
                                    {{-- {{ $Currency::Convert($perPillPrice) }} {{ __('text.common_per_unit', ['unit' => $unitLabel]) }} --}}
                                    {{ $Currency::Convert($perPillPrice) }}
                                @else
                                    {{ __('text.chat_from') }} {{ $Currency::Convert($minPack['price'] ?? $product['min_price'] ?? 0) }}
                                @endif
                            </span>
                            @if ($maxDiscount > 0)
                                <span class="card__discount">-{{ $maxDiscount }}%</span>
                            @endif
                        </div>
                        <button class="card__button button" type="button" aria-label="{{ __('text.product_add_to_cart_text') }}">
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#cart-white') }}"></use>
                                </svg>
                            </span>
                            {{-- <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#arrow-right') }}"></use>
                                </svg>
                            </span> --}}
                        </button>
                    </div>
                </article>
            </a>
        @endforeach
    </div>
</div>
@endif
