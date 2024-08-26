@extends($design . '.layouts.main')

@section('title', 'Category')

@section('content')
    <main class="main">
        @foreach ($products as $category)
            <h1>{{ $category['name'] }}</h1>
            <div class="product-cards">
                @foreach ($category['products'] as $product)
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
                            <div class="product-card__price">{{ $product['price'] }}</div>
    </main>
@endsection
