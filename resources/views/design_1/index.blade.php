@extends($design . '.layouts.main')

@section('title', $title)
@section('title_2', __('text.main_best_selling_title'))

@section('content')
<div class="main">
    <section class="page__best-sellers best-sellers" >
        <h2 class="best-sellers__title title">{{__('text.main_best_selling_title')}}</h2>
        <div class="best-sellers__items">
            @foreach ($bestsellers as $product)
                <a href="{{ route('home.product', $product['url']) }}" class="product-card">
                    <div class="product-card__body">
                        <div class="product-card__top">
                            <div class="product-card__info">
                                <h3 class="product-card__name">{{ $product['name'] }}</h3>
                                <h4 class="product-card__company">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv }}
                                    @endforeach
                                </h4>
                            </div>
                            <div class="product-card__price">{{ $Currency::convert($product['price'], false, true) }}</div>
                        </div>
                        <div class="product-card__image">
                            <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" width="140" height="140" alt="{{ $product['name'] }}">
                        </div>
                    </div>
                    <button type="button" class="product-card__button button button--primary">
                        <picture><source srcset="{{ asset("$design/images/icons/cart.webp") }}" type="image/webp"><img src="{{ asset("$design/images/icons/cart.png") }}" width="23" height="23" alt=""></picture>
                    </button>
                </a>
            @endforeach
        </div>
    </section>
    <!-- /.best-sellers -->
    <!-- END content -->
    </div>
    </div>
    </div>
    </div>

@endsection
