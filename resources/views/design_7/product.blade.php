@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<script>
    flagp = true;
</script>
<div class="product-box">
	<div class="holder-info">
		<div class="img">
			@if ($product['id'] != 616)
                <picture>
                    <source srcset="{{ asset('images/' . $product['image'] . '.webp') }}" type="image/webp">
                    <img loading="lazy" src="{{ asset('images/' . $product['image'] . '.webp') }}" alt="{{ $product['alt'] }}">
                </picture>
            @else
                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
            @endif
		</div>
		<div class="product">
			<h2>{{ $product['name'] }}</h2>
			@if ($product['id'] != 616)
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
                {{-- {$data.product_info.name}{if !$data.product_info.name|strstr:"Pack"} {$cur_product_packaging.dosage}{/if}{if $smarty.foreach.product_dosages.iteration == 1 && $data.product_info.rec_name != 'none'}<span style="font-weight:lighter;">, {#need_more#}</span> <span  class="details-page-product"><a href="{$path.page}/{$data.product_info.rec_url}" style="font-weight: normal;">{$data.product_info.rec_name}</a></span> {/if} --}}
                @if ($product['id'] != 616) {{ "{$product['name']} $key" }} @else {{ $product['name'] }} @endif
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
                    @if ($loop->remaining != 1 && $product['id'] != 616)
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

@if ($product['id'] == 616)
    <div class="description-box">
        <p>
            <strong>{{__('text.gift_card_title')}}</strong>
            <br>
            <br>
            <ol style="padding-left: 20px; line-height: 20px;">
                <li style="margin-bottom: 15px;">{{__('text.gift_card_text1')}}</li>
                <li>{{__('text.gift_card_text2')}}</li>
            </ol>
        </p>
    </div>
@endif

@endsection
