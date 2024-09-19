@extends($design . '.layouts.main')

@section('title', $title)

@section('announce')
    <div class="announce__item announce__item--yellow">
        <div class="announce__icon">
            <svg width="24" height="24">
                <use xlink:href="{{ asset($design . '/images/icons/icons.svg#svg-clock') }}"></use>
            </svg>
        </div>
        <div class="announce__text">{{__('text.common_cart1')}}<b>{{ucfirst(session('location.country_name'))}}{{__('text.common_cart2')}}</b></div>
    </div>
@endsection

@section('content')
<div class="column-box">
	<div class="basket" id="shopping_cart">

	</div>
	<div class="sidebar">
		<div class="information-banner">
			<div class="information">
				<div class="item">
					<strong class="name">
						<img src="{{ asset("$design/images/icon/2.png") }}" alt="">
						<span>{{__('text.cart_free_regular')}}</span>
					</strong>
					<p>{{__('text.cart_sum_regular')}}</p>
				</div>
				<div class="item">
					<strong class="name">
						<img src="{{ asset("$design/images/icon/3.png") }}" alt="">
						<span>{{__('text.cart_free_express')}}</span>
					</strong>
					<p>{{__('text.cart_sum_express')}}</p>
				</div>
				<div class="item">
					<strong class="name">
						<img src="{{ asset("$design/images/icon/4.png") }}" alt="">
						<span>{{__('text.cart_secret1')}} {{__('text.cart_secret2')}}</span>
					</strong>
					<p>{{__('text.cart_description_secret')}}</p>
				</div>
				<div class="item">
					<strong class="name">
						<img src="{{ asset("$design/images/icon/5.png") }}" alt="">
						<span>{{__('text.cart_moneyback1')}} {{__('text.cart_moneyback2')}}</span>
					</strong>
					<p>{{__('text.cart_description_moneyback')}}</p>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
