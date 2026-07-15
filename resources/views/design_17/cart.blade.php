@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="modal_cart hidden">
    <div class="bloktext">
    <p style="margin-bottom: 0">{{ __('text.common_cart1') }}<b>{{ ucfirst(session('location.country_name')) }} {{ __('text.common_cart2') }}</b></p>
    </div>
</div>
<script>
    flagc = true;
</script>
<div class="main__content" id="shopping_cart">
    <div class="cart-loader">
        <div class="cart-loader__spinner"></div>
        <p class="cart-loader__text">{{ __('text.cart_loading') }}</p>
    </div>
</div>
@endsection