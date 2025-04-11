@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')

<div class="christmas" style="display: none" onclick="location.href='{{ route('home.checkup') }}'">
    <img loading="lazy" src="{{ asset("/pub_images/checkup_img/white/checkup_big.png") }}">
</div>

<div class="product-box">
	<div class="holder-info">
		<div class="img">
            @if ($product['image'] == 'gift-card')
                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
            @else
                <picture>
                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                </picture>
            @endif
			{{-- @if ($product['image'] != 'gift-card')
                <picture>
                    <source srcset="{{ asset('images/' . $product['image'] . '.webp') }}" type="image/webp">
                    <img loading="lazy" src="{{ asset('images/' . $product['image'] . '.webp') }}" alt="{{ $product['image'] }}">
                </picture>
            @else
                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['alt'] }}">
            @endif --}}
		</div>
		<div class="product">
			<h2>{{ $product['name'] }}</h2>
			@if ($product['image'] != 'gift-card')
				<div class="info">
					@if (count($product['aktiv']) > 0)
						<span>{!!__('text.product_active')!!}</span>
						@foreach ($product['aktiv'] as $aktiv)
							<strong><a href="{{ route('home.active', $aktiv['url']) }}">{{ $aktiv['name'] }}</a></strong>
                        @endforeach
                    @endif
				</div>
				<div class="info">
					<span>{!!__('text.product_pack1_1')!!}</span>
					<strong><b  style="color: #f2d43a;">{{__('text.product_pack2_1')}}{{ random_int(10, 40) }}{{__('text.product_pack3_1')}}</b></strong>
				</div>
			@endif
			<div class="info">
				<p>{{ $product['desc'] }}</p>
			</div>
			@if (count($product['disease']) > 0)
				<div class="info">
					<span>{{__('text.product_diseases')}}</span>
					@foreach ($product['disease'] as $disease)
						<strong><a href="{{ route('home.disease', $disease['url']) }}">{{ ucfirst($disease['name']) }}</a></strong>
                    @endforeach
				</div>
            @endif
			@if (!empty($product['sinonim']))
				<div class="info">
					<span>{{ $product['name'] }} {!!__('text.product_others')!!}</span>
					@foreach ($product['sinonim'] as $sinonim)
                        @php
                            $sinonim['url'] = str_replace('.', ',', $sinonim['url']);
                        @endphp
						<strong>
                            <a href = "{{ route('home.product', $sinonim['url']) }}">
                                {{ $sinonim['name'] }}
                            </a>
                        </strong>
                    @endforeach
				</div>
            @endif
		</div>
	</div>
	<div class="links-box">
		@if (!empty($product['analog']))
			<h4>{{ $product['name'] }} {!!__('text.product_analogs')!!}</h4>
			<div class="text-box">
				<span class="text">
					@foreach ($product['analog'] as $analog)
						<a href="{{ route('home.product', $analog['url']) }}" class="analog">{{ $analog['name'] }}</a>
                    @endforeach
				</span>
				@if (count($product['analog']) > 10)<a href="#" class="more">view all</a>@endif
			</div>
        @endif
	</div>
</div>
@foreach ($product['packs'] as $key => $dosage)
    @php
        $prev_dosage = 0;
    @endphp
    @foreach ($dosage as $item)
        @if ($loop->last)
            @continue
        @endif
        @if ($loop->iteration != 1 && $key != $prev_dosage)
            </div>
            </div>
        @endif
        @if ($key != $prev_dosage)
            <div class="package-box">
            <h2>
                @if ($product['image'] != 'gift-card')
                {{ "{$product['name']} $key" }}@if ($loop->parent->iteration == 1 && $product['rec_name'] != 'none')<span style="font-weight:lighter;">, {{__('text.product_need_more')}}</span> <span class="details-page-product"><a href="{{route('home.product', $product['rec_url'])}}" style="font-weight: normal;">{{ $product['rec_name'] }}</a></span> @endif
                @else
                    {{ $product['name'] }}
                @endif
            </h2>
            <div class="package-table">
                <div class="head">
                    <div class="item">
                        <span>{{__('text.product_package_title')}}</span>
                        <span>{{__('text.product_price_per_pill_title')}}</span>
                        <span>{{__('text.product_price_title')}}</span>
                        <span></span>
                    </div>
                </div>
            @php
                $prev_dosage = $key;
            @endphp
        @endif
            <div class="body">
                <div class="item">
                    <div class="col">
                        <span>{{ "{$item['num']} {$product['type']}" }}</span>
                        @if ($item['price'] >= 300)
                            <span class="item-info-delivery">{{__('text.cart_free_express')}}</span>
                        @elseif($item['price'] < 300 && $item['price'] >= 200)
                            <span class="item-info-delivery">{{__('text.cart_free_regular')}}</span>
                        @endif
                    </div>
                    <div class="col">
                        <span>{{ $Currency::convert(round($item['price'] / $item['num'], 2), false, true) }}</span>
                    </div>
                    @if ($loop->remaining != 1 && $product['image'] != 'gift-card')
                        <div class="col"><span><span class="red">{{ $Currency::convert($dosage['max_pill_price'] * $item['num']) }} -{{ ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) }}%</span> {!!__('text.product_only')!!} {{ $Currency::convert($item['price']) }}</span></div>
                    @else
                        <div class="col"><span>{{ $Currency::convert($item['price']) }}</span></div>
                    @endif
                    <div class="col">
                        <form method="POST" action="{{ route('cart.add', $item['id']) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <img loading="lazy" src="{{ asset("$design/images/icon/ico-basket.svg") }}" alt="">
                                <span>{{__('text.product_add_to_cart_text_d2')}}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
    @endforeach
    </div>
</div>
@endforeach


@if ($product['full_desc'])
	<div class="description-box">
		{!! $product['full_desc'] !!}
	</div>
@endif

@if ($product['image'] == 'gift-card')
    <div class="description-box">
        <p style="margin: 0;">
            <strong>{{__('text.gift_card_title')}}</strong>
            <br>
            <br>
            <ol style="padding-left: 20px; line-height: 20px;">
                <li style="margin-bottom: 15px; list-style-type: decimal;">{{__('text.gift_card_text1')}}</li>
                <li style="list-style-type: decimal;">{{__('text.gift_card_text2')}}</li>
            </ol>
        </p>
    </div>
@endif

<h2 class="title-page">{{__('text.recc_text')}}</h2>
<div class="product-list product_rec">
    @foreach ($recommendation as $product_data)
        @if ($loop->iteration == 7)
            @break
        @endif
        <div class="item">
            @if ($product_data['discount'] != 0)
                <span class="card__label">-{{ $product_data['discount'] }}%</span>
            @endif
            <a href="{{ route('home.product', $product_data['url']) }}" class="img">
                <picture>
                    <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                    <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}">
                </picture>
            </a>
            <div class="info">
                <div class="box">
                    <a href="{{ route('home.product', $product_data['url']) }}" class="name">{{ $product_data['name'] }}</a>
                    <a href="{{ route('home.product', $product_data['url']) }}" class="cat">
                        @foreach ($product_data['aktiv'] as $aktiv)
                            {{ $aktiv['name'] }}
                        @endforeach
                    </a>
                </div>
                <div class="box">
                    <span class="price">{{ $Currency::convert($product_data['price'], false, true) }}</span>
                    <a href="{{ route('home.product', $product_data['url']) }}" class="btn btn-primary main">
                        <img loading="lazy" src="{{ asset("$design/images/icon/ico-basket.svg") }}" alt="">
                        <span>{{__('text.common_add_to_cart_text_d2')}}</span>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

@endsection
