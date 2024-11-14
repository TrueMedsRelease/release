@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)
@section('announce_color', 'announce__item--yellow')
@section('announce_img', asset($design . '/images/icon/icons.svg#svg-clock'))
@section('announce_text_1', __('text.common_cart1'))
@section('announce_text_2', ucfirst(session('location.country_name')) . ' ' . __('text.common_cart2'))

@section('content')
<div class="column-box">
	<div class="basket" id="shopping_cart">

	</div>
	<div class="sidebar">
		<div class="information-banner">
			<div class="information">
				<div class="item">
					<strong class="name">
						<img loading="lazy" src="{{ asset("$design/images/icon/2.png") }}" alt="">
						<span>{{__('text.cart_free_regular')}}</span>
					</strong>
					<p>{{__('text.cart_sum_regular')}}</p>
				</div>
				<div class="item">
					<strong class="name">
						<img loading="lazy" src="{{ asset("$design/images/icon/3.png") }}" alt="">
						<span>{{__('text.cart_free_express')}}</span>
					</strong>
					<p>{{__('text.cart_sum_express')}}</p>
				</div>
				<div class="item">
					<strong class="name">
						<img loading="lazy" src="{{ asset("$design/images/icon/4.png") }}" alt="">
						<span>{{__('text.cart_secret1')}} {{__('text.cart_secret2')}}</span>
					</strong>
					<p>{{__('text.cart_description_secret')}}</p>
				</div>
				<div class="item">
					<strong class="name">
						<img loading="lazy" src="{{ asset("$design/images/icon/5.png") }}" alt="">
						<span>{{__('text.cart_moneyback1')}} {{__('text.cart_moneyback2')}}</span>
					</strong>
					<p>{{__('text.cart_description_moneyback')}}</p>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
