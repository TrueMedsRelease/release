@php
$product = $product ?? [];
$packs = $product['packs'] ?? [];
$packsByDosage = collect($packs)->groupBy('dosage');
@endphp

<div class="chat-row chat-row--page chat-message--appear js-chat-product-detail">
    <div class="chat-message">
        <div class="chat-message__content content"></div>
        <div class="chat-message__page">
            <div class="product-card">
                @if (!empty($product['image']))
                    <div class="product-card__image">
                        <picture>
                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] ?? '' }}" loading="lazy">
                        </picture>
                    </div>
                @endif
                <div class="product-card__content">
                    <div class="product-card__name h1">{{ $product['name'] ?? '' }}</div>
                    {{-- @if (!empty($product['dosage']))
                        <div class="product-card__description">{{ $product['dosage'] }}</div>
                    @endif --}}
                    @if (!empty($variantDosages))
                        <div class="card__variants">
                            @foreach ($variantDosages as $dosage)
                                <div class="card__variant">{{ $dosage }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if ($packsByDosage->isNotEmpty())
                @foreach ($packsByDosage as $dosage => $dosagePacks)
                    <div class="panel">
                        <div class="panel__header">
                            <h2 class="h2">{{ $product['name'] ?? '' }} {{ $dosage }}</h2>
                        </div>
                        <table class="table product-table">
                            <thead>
                                <tr>
                                    <th>{{ __('text.chat_package') }}</th>
                                    <th>{{ __('text.chat_per_item') }}</th>
                                    <th>{{ __('text.chat_price') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dosagePacks as $pack)
                                    <tr class="product">
                                        <td class="product__info-wrapper">
                                            <div class="product__info">
                                                <div class="product__quantity">
                                                    {{ $pack['quantity'] ?? '' }}
                                                    @if (!empty($pack['unit'])) {{ $pack['unit'] }} @endif
                                                </div>
                                                @if (!empty($pack['delivery']))
                                                    <div class="product__delivery">{{ $pack['delivery'] }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="product__price-per-pill">
                                            @if (!empty($pack['price_per_pill']))
                                                {{ $Currency::Convert($pack['price_per_pill']) }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="product__price-wrapper">
                                                @if (!empty($pack['old_price']) && $pack['old_price'] > $pack['price'])
                                                    <div class="product__discount">
                                                        <s>{{ $Currency::Convert($pack['old_price']) }}</s>
                                                        <span>-{{ round((1 - $pack['price'] / $pack['old_price']) * 100) }}%</span>
                                                    </div>
                                                @endif
                                                <div class="product__price">{{ $Currency::Convert($pack['price'] ?? 0) }}</div>
                                            </div>
                                        </td>
                                        <td class="product__button-wrapper">
                                            <form action="{{ $pack['add_url'] ?? '#' }}" method="post" class="product__form">
                                                @csrf
                                                <button class="button product__button" type="submit">
                                                    <span class="icon">
                                                        <svg width="1em" height="1em" fill="currentColor">
                                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#cart-white') }}"></use>
                                                        </svg>
                                                    </span>
                                                    <span class="button__text">{{ __('text.chat_add_to_cart') }}</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @else
                <p class="product-detail__empty">{{ __('text.chat_no_packs') }}</p>
            @endif
        </div>
    </div>
</div>
