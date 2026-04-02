@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<main class="page">
    <section class="page__bestsellers bestsellers">
        <aside class="categories-sidebar">
            <div class="categories-sidebar__inner">
                <div data-spollers class="categories-sidebar__spollers spollers">
                    <div class="spollers__item">
                        <button type="button" data-spoller class="spollers__title _spoller-active">{{__('text.main_best_selling_title')}}</button>
                        <ul class="spollers__body main_bestsellers" id="main_bestsellers_body">
                            @foreach ($bestsellers as $bestseller)
                                <li class="spollers__item-list"><a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span style="font-size: 12px;">{{ $Currency::convert($bestseller['price'], false, true) }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    @foreach ($menu as $category)
                        <div class="spollers__item">
                            <button type="button" data-spoller class="spollers__title _spoller-active">{{ $category['name'] }}</button>
                            <ul class="spollers__body" id="this_product_category">
                                @foreach ($category['products'] as $item)
                                    <li class="spollers__item-list">
                                        <a href="{{ route('home.product', $item['url']) }}">
                                            {{ $item['name'] }}
                                        </a>
                                        <span style="font-size: 12px;">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
        <div class="bestsellers__container">
            {{-- <div class="bonus_block">
                <div class="bonus1">
                    <img loading="lazy" src="{{ asset("$design/images/bonus1_1.png") }}">
                </div>
                <div class="bonus2">
                    <img loading="lazy" src="{{ asset("$design/images/bonus2_2.png") }}">
                </div>
            </div> --}}

            <div class="bonus_block all_padding big">
                <div class="bonus1">
                    <a href="{{ route('home.bonus_referral_program') }}">
                        <img loading="lazy" src="{{ asset("$design/images/bonus_programm.png") }}">
                    </a>
                </div>
                <div class="bonus1">
                    <img loading="lazy" src="{{ asset("$design/images/happy_day.png") }}">
                </div>
                <div class="bonus2">
                    <img loading="lazy" src="{{ asset("$design/images/super_sale.png") }}">
                </div>
            </div>
            <div class="bonus_block all_padding small">
                <div class="bonus1">
                    <a href="{{ route('home.bonus_referral_program') }}">
                        <img loading="lazy" src="{{ asset("$design/images/bonus_1_small.png") }}">
                    </a>
                </div>
                <div class="bonus1">
                    <img loading="lazy" src="{{ asset("$design/images/bonus_2_small.png") }}">
                </div>
                <div class="bonus2">
                    <img loading="lazy" src="{{ asset("$design/images/bonus_3_small.png") }}">
                </div>
            </div>
            
            <h2 class="bestsellers__title title">{{__('text.aktiv_aktiv_result_title')}} {{ ucwords(str_replace('-', ' ', $active)) }}</h2>
            <div class="bestsellers__body">
                <div class="product_list">
                    @foreach ($products as $product)
                        <div class="product_info">
                            @if ($product['id'] != 616 && $product['discount'] != 0)
                                <span class="card__label">-{{ $product['discount'] }}%</span>
                            @endif
                            <div class="product_info_top">
                                <a href="{{ route('home.product', $product['url']) }}">
                                    <div class="product_img">
                                        @if ($product['id'] == 616)
                                            <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                                        @else
                                            <picture>
                                                <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                                <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                                            </picture>
                                        @endif
                                        {{-- <img loading="lazy" src="{{ $product['id'] != 616 ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}"> --}}
                                    </div>
                                </a>
                                <a href="{{ route('home.product', $product['url']) }}" class="product_center">
                                    <div class="product_main">
                                        <div class="product_text">
                                            <span class="product_name">{{ $product['name'] }}</span>
                                            <span class="product_active">
                                                @foreach ($product['aktiv'] as $aktiv)
                                                    {{ $aktiv['name'] }}
                                                @endforeach
                                            </span>
                                        </div>
                                        <div class="product_desc top">{{ $product['desc'] }}</div>
                                    </div>
                                </a>
                                <div class="product_right_block">
                                    <div class="product_price">{{ $Currency::convert($product['price'], false, true) }}</div>
                                    <button type="button" class="product-card__button button button--accent" title="{{__('text.product_add_to_cart_text')}}" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="24" height="24">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart-white") }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="24" height="24">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.4697 3.46967C14.7626 3.17678 15.2375 3.17678 15.5304 3.46967L19.3107 7.25001H22C22.4142 7.25001 22.75 7.5858 22.75 8.00001C22.75 8.41422 22.4142 8.75001 22 8.75001H21.4557C21.2293 9.60606 20.9365 10.593 20.5959 11.7414L19.5708 15.1969C19.1636 16.5702 18.914 17.412 18.4765 18.0966C17.7233 19.2751 16.5663 20.1386 15.2223 20.5256C14.4416 20.7503 13.5635 20.7502 12.1312 20.75H11.8688C10.4365 20.7502 9.55843 20.7503 8.77772 20.5256C7.43365 20.1386 6.2767 19.2751 5.52349 18.0966C5.08598 17.412 4.83637 16.5702 4.42921 15.1969L3.40431 11.742C3.06357 10.5934 2.77073 9.60621 2.54431 8.75001H2C1.58579 8.75001 1.25 8.41422 1.25 8.00001C1.25 7.5858 1.58579 7.25001 2 7.25001H4.93198L8.71231 3.46969C9.0052 3.17679 9.48008 3.17679 9.77297 3.46969C10.0659 3.76258 10.0659 4.23745 9.77297 4.53035L7.05331 7.25001H17.1894L14.4697 4.53033C14.1768 4.23744 14.1768 3.76256 14.4697 3.46967ZM10.75 12C10.75 11.5858 10.4142 11.25 10 11.25C9.58579 11.25 9.25 11.5858 9.25 12V16C9.25 16.4142 9.58579 16.75 10 16.75C10.4142 16.75 10.75 16.4142 10.75 16V12ZM14.75 12C14.75 11.5858 14.4142 11.25 14 11.25C13.5858 11.25 13.25 11.5858 13.25 12V16C13.25 16.4142 13.5858 16.75 14 16.75C14.4142 16.75 14.75 16.4142 14.75 16V12Z" fill="white"/>
                                            </svg>
                                        @endif
                                        <span>{{__('text.common_buy_button')}}</span>
                                    </button>
                                </div>
                            </div>
                            <div class="product_info_bottom">
                                <div class="product_desc bottom">{{ $product['desc'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</main>

@section('testimonial')
    <div class="reviews_block">
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_1')!!}</div>
                <div class="stars">
                    <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_1')}}</div>
        </div>
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_7')!!}</div>
                <div class="stars">
                    <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_7')}}</div>
        </div>
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_13')!!}</div>
                <div class="stars">
                    <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_13')}}</div>
        </div>
        <div class="review">
            <div class="review_top">
                <div class="person_name">{!!__('text.testimonials_author_t_17')!!}</div>
                <div class="stars">
                    <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
                </div>
            </div>
            <div class="review_text">{{__('text.testimonials_t_17')}}</div>
        </div>
    </div>
@endsection

@endsection
