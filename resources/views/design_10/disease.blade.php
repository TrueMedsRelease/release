@extends($design . '.layouts.main')

@section('title', str_replace('-', ' ', $disease))

@section('content')
<main class="main">
    <h1>{{__('text.disease_disease_result_title')}} «{{ str_replace('-', ' ', $disease) }}»</h1>
    <div class="product-cards">
        @foreach ($products as $product)
        <article class="card product-card">
            <a class="product-card__img" href="{{ route('home.product', $product['url']) }}">
                <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/products/gift-card.svg') }}" width="140" height="140" alt="{{ $product['name'] }}">
            </a>
            <h2 class="product-card__heading">
                <a class="product-card__brand link-primary" href="{{ route('home.product', $product['url']) }}">{{ $product['name'] }}</a>
            </h2>
            @foreach ($product['aktiv'] as $aktiv)
            <a class="product-card__ingredient" href="{{ route('home.active', $aktiv) }}">{{ $aktiv }}</a>
            @endforeach
            <a class="product-card__text link-primary" href="{{ route('home.product', $product['url']) }}">
                {{ str()->limit($product['desc'], 120, $end='...') }}
            </a>
            <div class="product-card__controls">
                <button class="button product-card__button" aria-label="Buy Now" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg#cart") }}"></use>
                        </svg>
                    </span> <span class="button__text">{{__('text.common_buy_button')}}</span>
                </button>
            <div class="product-card__price">${{ $product['price'] }}</div>
            </div>
        </article>
        @if ($loop->index == 1)
        <div class="combo-cards">
            <article class="card combo-card">
                <a class="link-primary combo-card__link-wrapper" href="">
                    <h2 class="combo-card__title">Happy Day</h2>
                    <div class="combo-card__text">Big Discounts <br>Only Today</div>
                </a>
            </article>
            <article class="card combo-card combo-card--sale">
                <a class="link-primary combo-card__link-wrapper" href="">
                    <h2 class="combo-card__title">Wow! <br>Super sale</h2>
                    <div class="combo-card__text">Big Packs - 80%</div>
                </a>
            </article>
        </div>
        @endif
        @endforeach
    </div>
  </main>
  @endsection
