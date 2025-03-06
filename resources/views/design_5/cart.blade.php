@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)
@section('announce_color', 'announce__item--yellow')
@section('announce_img', asset($design . '/images/icon/icons.svg#svg-clock'))
@section('announce_text_1', __('text.common_cart1'))
@section('announce_text_2', ucfirst(session('location.country_name')) . ' ' . __('text.common_cart2'))

@section('content')

<div class="christmas" style="display: none" onclick="location.href='{{ route('home.checkup') }}'">
    <img loading="lazy" src="{{ asset("/pub_images/checkup_img/white/checkup_big.png") }}">
</div>

<div id="shopping_cart">

</div>

@endsection
