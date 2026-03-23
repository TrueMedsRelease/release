@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-wrapper container">
    @foreach ($products as $category)
        <main class="main">
            <h1 class="h1">{{ $category['name'] }}</h1>
            <div class="product-cards">
                <div class="cards">
                    @foreach ($category['products'] as $product)
                        <article class="card @if ($product['id'] == 616) card--gift @endif">
                            <div class="card__header">
                                <h2 class="card__title">
                                    <a href="{{ route('home.product', $product['url']) }}">{{ $product['name'] }}</a>
                                </h2>
                                @if (!in_array($product['id'], [616, 619, 620, 483, 484, 501, 615]))
                                    <div class="card__variants">
                                        @foreach ($product['product_dosages'] as $dosage)
                                            <span class="card__variant">{{ $dosage }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="card__img">
                                @if ($product['id'] == 616)
                                    <picture>
                                        <source type="image/webp" srcset="{{ asset("$design/img/products/gift-137w.webp") }} 1x, {{ asset("$design/img/products/gift-274w.webp") }} 2x"><img
                                            src="{{ asset("$design/img/products/gift-137w.png") }}"
                                            srcset="{{ asset("$design/img/products/gift-137w.png") }} 1x, {{ asset("$design/img/products/gift-274w.png") }} 2x" width="137" height="183"
                                            alt="{{ $product['image'] }}">
                                    </picture>
                                @else
                                    <picture style="max-height: 200px; max-width: 200px;">
                                        <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                        <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="max-height: 200px; max-width: 200px;">
                                    </picture>
                                @endif
                            </div>
                            <div class="card__footer">
                                <div class="card__price-wrapper">
                                    <span class="card__price">{{ $Currency::convert($product['price'], false, true) }}</span>
                                </div>
                                <button class="card__button button button--secondary" onclick="location.href = '{{ route('home.product', $product['url']) }}'">
                                    <span class="icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#cart") }}"></use>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                <path d="M8 18C6.9 18 6.01 18.9 6.01 20C6.01 21.1 6.9 22 8 22C9.1 22 10 21.1 10 20C10 18.9 9.1 18 8 18ZM2 2V4H4L7.6 11.59L6.25 14.04C6.09 14.32 6 14.65 6 15C6 16.1 6.9 17 8 17H20V15H8.42C8.28 15 8.17 14.89 8.17 14.75L8.2 14.63L9.1 13H16.55C17.3 13 17.96 12.59 18.3 11.97L21.88 5.48C21.96 5.34 22 5.17 22 5C22 4.45 21.55 4 21 4H6.21L5.27 2H2ZM18 18C16.9 18 16.01 18.9 16.01 20C16.01 21.1 16.9 22 18 22C19.1 22 20 21.1 20 20C20 18.9 19.1 18 18 18Z" fill="currentColor" />
                                            </svg>
                                        @endif
                                    </span>
                                    {{ __('text.common_add_to_cart_text_d2') }}
                                </button>
                            </div>

                            {{-- @if ($product['id'] != 616)
                                <div class="card-features">
                                    @if ($product['discount'] != 0)
                                        <div class="card-feature card-feature--discount">-{{ $product['discount'] }}%</div>
                                    @endif
                                </div>
                            @endif --}}
                        </article>
                    @endforeach
                </div>
            </div>
        </main>
    @endforeach
    <aside class="aside">
        <div class="accordion aside-nav" data-accordion>
            <details class="accordion-item @if($cur_category == '') is-open @endif" data-accordion-item @if($cur_category == '') open @endif>
                <summary class="accordion-button" data-accordion-button>
                    <span class="button-text">{{__('text.common_best_selling_title')}}</span>
                    <span class="icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em"  fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#paw") }}"></use>
                            </svg>
                        @else
                            <svg viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"  fill="currentColor">
                                <g clip-path="url(#paw_clip0)">
                                    <path d="M15.5154 6.7575C15.249 6.20402 14.8514 5.72436 14.3573 5.36045C13.8632 4.99654 13.2876 4.75948 12.6809 4.67C11.3186 4.495 8.95567 5.4075 7.85282 7.225C8.99903 7.60681 9.99433 8.34401 10.6948 9.33C10.7125 9.35769 10.7245 9.38863 10.7302 9.42101C10.7359 9.45339 10.7351 9.48658 10.7279 9.51866C10.7207 9.55074 10.7072 9.58107 10.6883 9.6079C10.6693 9.63474 10.6452 9.65754 10.6174 9.675C10.5613 9.70956 10.4939 9.7208 10.4295 9.70633C10.3652 9.69186 10.3091 9.65281 10.2731 9.5975C9.62844 8.67483 8.69371 7.9953 7.61828 7.6675C7.61828 7.67 7.6158 7.67 7.6158 7.6725C6.77867 7.43434 5.89443 7.41626 5.04831 7.62C5.04832 7.61967 5.04826 7.61934 5.04813 7.61903C5.04801 7.61873 5.04783 7.61845 5.04759 7.61822C5.04736 7.61798 5.04708 7.6178 5.04678 7.61768C5.04647 7.61755 5.04614 7.61749 5.04581 7.6175C4.6103 7.72074 4.18518 7.86384 3.7758 8.045C3.7458 8.05842 3.71345 8.06577 3.6806 8.06664C3.64776 8.06751 3.61507 8.06188 3.5844 8.05007C3.55374 8.03825 3.5257 8.02049 3.5019 7.9978C3.4781 7.9751 3.459 7.94793 3.4457 7.91783C3.4324 7.88772 3.42517 7.85529 3.4244 7.82238C3.42364 7.78947 3.42937 7.75673 3.44126 7.72604C3.45315 7.69535 3.47097 7.66732 3.49369 7.64355C3.51642 7.61977 3.5436 7.60073 3.57369 7.5875C3.97043 7.412 4.38105 7.26984 4.80129 7.1625C4.3437 6.10402 4.14769 4.95056 4.2299 3.8L5.17305 3.9875L4.76635 2.7225C4.74959 2.66997 4.75052 2.61337 4.76901 2.56143C4.78749 2.50948 4.8225 2.46506 4.86865 2.435L5.47247 2.04C5.52805 2.00419 5.59554 1.9919 5.66014 2.00582C5.72475 2.01975 5.78122 2.05876 5.81719 2.1143C5.85316 2.16985 5.8657 2.23741 5.85206 2.3022C5.83843 2.36699 5.79973 2.42373 5.74444 2.46L5.30031 2.75L5.63964 3.81L6.13867 3.51C6.38573 3.36321 6.58522 3.14805 6.71315 2.89037C6.84107 2.63269 6.89198 2.34348 6.85976 2.0575C6.73271 1.33036 6.41592 0.649833 5.94155 0.0849984C5.91327 0.0524463 5.87695 0.0279189 5.83622 0.0138773C5.79549 -0.00016441 5.75179 -0.00322645 5.70951 0.00499838L2.96487 0.504998C2.91113 0.51496 2.86209 0.542162 2.82514 0.582498C0.215237 3.46 0.0231124 10.4475 0.015627 10.745C0.0154028 10.8018 0.0338117 10.8572 0.0680247 10.9025C0.185296 11.0575 3.03972 14.7375 7.48604 15H7.50101C7.51609 14.9996 7.53111 14.9979 7.54592 14.995C8.39365 14.8085 9.17702 14.4005 9.81648 13.8125C9.24168 13.5564 8.70911 13.2141 8.23707 12.7975C8.18925 12.7521 8.16114 12.6897 8.15881 12.6238C8.15648 12.5579 8.1801 12.4936 8.22459 12.445C8.27038 12.3971 8.33303 12.3689 8.39921 12.3666C8.46539 12.3643 8.52987 12.3879 8.5789 12.4325C9.05354 12.8535 9.59603 13.1908 10.1833 13.43L10.1858 13.4275C11.5182 13.965 13.6615 14.155 15.3432 11.1325C15.7585 10.4033 15.9801 9.57954 15.9869 8.74C15.9877 8.05118 15.8261 7.37191 15.5154 6.7575ZM3.63606 12.41C3.61351 12.4444 3.58279 12.4727 3.54665 12.4924C3.5105 12.512 3.47007 12.5224 3.42896 12.5225C3.37925 12.5223 3.33068 12.5075 3.28923 12.48C2.48019 11.9353 1.74108 11.293 1.08853 10.5675C1.06674 10.5429 1.05001 10.5142 1.03928 10.4831C1.02855 10.452 1.02405 10.4191 1.02601 10.3862C1.02798 10.3534 1.03639 10.3212 1.05076 10.2916C1.06512 10.262 1.08517 10.2356 1.10974 10.2137C1.13431 10.1919 1.16294 10.1751 1.19398 10.1644C1.22503 10.1537 1.25788 10.1491 1.29066 10.1511C1.32345 10.1531 1.35552 10.1615 1.38506 10.1759C1.41459 10.1903 1.44101 10.2104 1.4628 10.235C2.08569 10.9298 2.79222 11.5445 3.56619 12.065C3.62107 12.1015 3.65924 12.1583 3.67234 12.223C3.68544 12.2877 3.67239 12.3549 3.63606 12.41ZM14.5148 7.545C14.48 7.56259 14.4416 7.572 14.4025 7.5725C14.3563 7.57291 14.311 7.56021 14.2717 7.53588C14.2324 7.51155 14.2008 7.47657 14.1805 7.435C14.0246 7.10038 13.7895 6.80904 13.4955 6.58637C13.2015 6.3637 12.8576 6.21647 12.4938 6.1575C12.4611 6.15366 12.4295 6.14338 12.4009 6.12725C12.3722 6.11112 12.347 6.08946 12.3267 6.06352C12.3064 6.03757 12.2915 6.00786 12.2827 5.9761C12.274 5.94434 12.2716 5.91116 12.2757 5.87847C12.2798 5.84578 12.2904 5.81423 12.3067 5.78565C12.3231 5.75706 12.3449 5.73201 12.371 5.71193C12.3971 5.69184 12.4269 5.67714 12.4586 5.66866C12.4904 5.66017 12.5236 5.65808 12.5561 5.6625C13.0012 5.73069 13.4226 5.90751 13.7833 6.17737C14.144 6.44724 14.4329 6.80187 14.6246 7.21C14.6543 7.26902 14.6594 7.33745 14.6389 7.40025C14.6183 7.46306 14.5737 7.51512 14.5148 7.545Z" fill="currentColor" />
                                </g>
                                <defs>
                                    <clipPath id="paw_clip0">
                                    <rect width="16" height="15" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        @endif
                    </span>
                </summary>
                <div class="accordion-panel" data-accordion-panel>
                    <div class="accordion-content content">
                        <ul class="aside-nav__list">
                            @foreach ($bestsellers as $bestseller)
                                <li class="aside-nav__item">
                                    <a class="aside-nav__link" href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}
                                        <span class="aside-nav__price">{{ $Currency::convert($bestseller['price'], false, true) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </details>
            @foreach ($menu as $category)
                <details class="accordion-item @if(strtolower($cur_category) == strtolower($category['name'])) is-open @endif" data-accordion-item @if(strtolower($cur_category) == strtolower($category['name'])) open @endif>
                    <summary class="accordion-button" data-accordion-button>
                        <span class="button-text">{{ $category['name'] }}</span>
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em"  fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#paw") }}"></use>
                                </svg>
                            @else
                                <svg viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"  fill="currentColor">
                                    <g clip-path="url(#paw_clip0)">
                                        <path d="M15.5154 6.7575C15.249 6.20402 14.8514 5.72436 14.3573 5.36045C13.8632 4.99654 13.2876 4.75948 12.6809 4.67C11.3186 4.495 8.95567 5.4075 7.85282 7.225C8.99903 7.60681 9.99433 8.34401 10.6948 9.33C10.7125 9.35769 10.7245 9.38863 10.7302 9.42101C10.7359 9.45339 10.7351 9.48658 10.7279 9.51866C10.7207 9.55074 10.7072 9.58107 10.6883 9.6079C10.6693 9.63474 10.6452 9.65754 10.6174 9.675C10.5613 9.70956 10.4939 9.7208 10.4295 9.70633C10.3652 9.69186 10.3091 9.65281 10.2731 9.5975C9.62844 8.67483 8.69371 7.9953 7.61828 7.6675C7.61828 7.67 7.6158 7.67 7.6158 7.6725C6.77867 7.43434 5.89443 7.41626 5.04831 7.62C5.04832 7.61967 5.04826 7.61934 5.04813 7.61903C5.04801 7.61873 5.04783 7.61845 5.04759 7.61822C5.04736 7.61798 5.04708 7.6178 5.04678 7.61768C5.04647 7.61755 5.04614 7.61749 5.04581 7.6175C4.6103 7.72074 4.18518 7.86384 3.7758 8.045C3.7458 8.05842 3.71345 8.06577 3.6806 8.06664C3.64776 8.06751 3.61507 8.06188 3.5844 8.05007C3.55374 8.03825 3.5257 8.02049 3.5019 7.9978C3.4781 7.9751 3.459 7.94793 3.4457 7.91783C3.4324 7.88772 3.42517 7.85529 3.4244 7.82238C3.42364 7.78947 3.42937 7.75673 3.44126 7.72604C3.45315 7.69535 3.47097 7.66732 3.49369 7.64355C3.51642 7.61977 3.5436 7.60073 3.57369 7.5875C3.97043 7.412 4.38105 7.26984 4.80129 7.1625C4.3437 6.10402 4.14769 4.95056 4.2299 3.8L5.17305 3.9875L4.76635 2.7225C4.74959 2.66997 4.75052 2.61337 4.76901 2.56143C4.78749 2.50948 4.8225 2.46506 4.86865 2.435L5.47247 2.04C5.52805 2.00419 5.59554 1.9919 5.66014 2.00582C5.72475 2.01975 5.78122 2.05876 5.81719 2.1143C5.85316 2.16985 5.8657 2.23741 5.85206 2.3022C5.83843 2.36699 5.79973 2.42373 5.74444 2.46L5.30031 2.75L5.63964 3.81L6.13867 3.51C6.38573 3.36321 6.58522 3.14805 6.71315 2.89037C6.84107 2.63269 6.89198 2.34348 6.85976 2.0575C6.73271 1.33036 6.41592 0.649833 5.94155 0.0849984C5.91327 0.0524463 5.87695 0.0279189 5.83622 0.0138773C5.79549 -0.00016441 5.75179 -0.00322645 5.70951 0.00499838L2.96487 0.504998C2.91113 0.51496 2.86209 0.542162 2.82514 0.582498C0.215237 3.46 0.0231124 10.4475 0.015627 10.745C0.0154028 10.8018 0.0338117 10.8572 0.0680247 10.9025C0.185296 11.0575 3.03972 14.7375 7.48604 15H7.50101C7.51609 14.9996 7.53111 14.9979 7.54592 14.995C8.39365 14.8085 9.17702 14.4005 9.81648 13.8125C9.24168 13.5564 8.70911 13.2141 8.23707 12.7975C8.18925 12.7521 8.16114 12.6897 8.15881 12.6238C8.15648 12.5579 8.1801 12.4936 8.22459 12.445C8.27038 12.3971 8.33303 12.3689 8.39921 12.3666C8.46539 12.3643 8.52987 12.3879 8.5789 12.4325C9.05354 12.8535 9.59603 13.1908 10.1833 13.43L10.1858 13.4275C11.5182 13.965 13.6615 14.155 15.3432 11.1325C15.7585 10.4033 15.9801 9.57954 15.9869 8.74C15.9877 8.05118 15.8261 7.37191 15.5154 6.7575ZM3.63606 12.41C3.61351 12.4444 3.58279 12.4727 3.54665 12.4924C3.5105 12.512 3.47007 12.5224 3.42896 12.5225C3.37925 12.5223 3.33068 12.5075 3.28923 12.48C2.48019 11.9353 1.74108 11.293 1.08853 10.5675C1.06674 10.5429 1.05001 10.5142 1.03928 10.4831C1.02855 10.452 1.02405 10.4191 1.02601 10.3862C1.02798 10.3534 1.03639 10.3212 1.05076 10.2916C1.06512 10.262 1.08517 10.2356 1.10974 10.2137C1.13431 10.1919 1.16294 10.1751 1.19398 10.1644C1.22503 10.1537 1.25788 10.1491 1.29066 10.1511C1.32345 10.1531 1.35552 10.1615 1.38506 10.1759C1.41459 10.1903 1.44101 10.2104 1.4628 10.235C2.08569 10.9298 2.79222 11.5445 3.56619 12.065C3.62107 12.1015 3.65924 12.1583 3.67234 12.223C3.68544 12.2877 3.67239 12.3549 3.63606 12.41ZM14.5148 7.545C14.48 7.56259 14.4416 7.572 14.4025 7.5725C14.3563 7.57291 14.311 7.56021 14.2717 7.53588C14.2324 7.51155 14.2008 7.47657 14.1805 7.435C14.0246 7.10038 13.7895 6.80904 13.4955 6.58637C13.2015 6.3637 12.8576 6.21647 12.4938 6.1575C12.4611 6.15366 12.4295 6.14338 12.4009 6.12725C12.3722 6.11112 12.347 6.08946 12.3267 6.06352C12.3064 6.03757 12.2915 6.00786 12.2827 5.9761C12.274 5.94434 12.2716 5.91116 12.2757 5.87847C12.2798 5.84578 12.2904 5.81423 12.3067 5.78565C12.3231 5.75706 12.3449 5.73201 12.371 5.71193C12.3971 5.69184 12.4269 5.67714 12.4586 5.66866C12.4904 5.66017 12.5236 5.65808 12.5561 5.6625C13.0012 5.73069 13.4226 5.90751 13.7833 6.17737C14.144 6.44724 14.4329 6.80187 14.6246 7.21C14.6543 7.26902 14.6594 7.33745 14.6389 7.40025C14.6183 7.46306 14.5737 7.51512 14.5148 7.545Z" fill="currentColor" />
                                    </g>
                                    <defs>
                                        <clipPath id="paw_clip0">
                                        <rect width="16" height="15" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            @endif
                        </span>
                    </summary>
                    <div class="accordion-panel" data-accordion-panel>
                        <div class="accordion-content content">
                            <ul class="aside-nav__list">
                                @foreach ($category['products'] as $item)
                                    <li class="aside-nav__item">
                                        <a class="aside-nav__link" href="{{ route('home.product', $item['url']) }}">{{ $item['name'] }}
                                            <span class="aside-nav__price">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </details>
            @endforeach
        </div>
    </aside>
</div>
@endsection

@section('rewies')
<div class="footer-testimonials">
    <div class="testimonial">
        <div class="testimonial__header">
            <div class="testimonial__author">{!!__('text.testimonials_author_t_1')!!}</div>
            <div class="testimonial__rating">
                <div class="rating">
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                </div>
            </div>
        </div>
        <div class="testimonial__text">{{__('text.testimonials_t_1')}}</div>
    </div>
    <div class="testimonial">
        <div class="testimonial__header">
            <div class="testimonial__author">{!!__('text.testimonials_author_t_7')!!}</div>
            <div class="testimonial__rating">
                <div class="rating">
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                </div>
            </div>
        </div>
        <div class="testimonial__text">{{__('text.testimonials_t_7')}}</div>
    </div>
    <div class="testimonial">
        <div class="testimonial__header">
            <div class="testimonial__author">{!!__('text.testimonials_author_t_13')!!}</div>
            <div class="testimonial__rating">
                <div class="rating">
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                </div>
            </div>
        </div>
        <div class="testimonial__text">{{__('text.testimonials_t_13')}}</div>
    </div>
    <div class="testimonial">
        <div class="testimonial__header">
            <div class="testimonial__author">{!!__('text.testimonials_author_t_17')!!}</div>
            <div class="testimonial__rating">
                <div class="rating">
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                    <div class="rating__star"></div>
                </div>
            </div>
        </div>
        <div class="testimonial__text">{{__('text.testimonials_t_17')}}</div>
    </div>
</div>
@endsection