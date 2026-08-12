@if (!empty($products))
    @foreach ($products as $product)
        @include($design . '.ajax.search_product_card', ['product' => $product])
    @endforeach
@endif
