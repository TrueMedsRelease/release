@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-wrapper container">
    <main class="main">
        <h1 class="h1">{{__('text.first_letter_first_letter_result_title')}} «{{ $letter }}»</h1>
        <div class="product-cards">
            <div class="cards">
                @foreach ($products as $product)
                    <article class="card">
                        <div class="card__img">
                            @if ($product['id'] == 616)
                                <picture>
                                    <source type="image/webp" srcset="{{ asset("$design/img/products/gift-200w.webp") }} 1x, {{ asset("$design/img/products/gift-400w.webp 2x") }}">
                                    <img src="{{ asset("$design/img/products/gift-200w.jpg") }}" srcset="{{ asset("$design/img/products/gift-200w.jpg") }} 1x, {{ asset("$design/img/products/gift-400w.jpg 2x") }}"
                                    width="200" height="200" alt="{{ $product['image'] }}">
                                </picture>
                            @else
                                <picture style="max-height: 200px; max-width: 200px;">
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}" style="max-height: 200px; max-width: 200px;">
                                </picture>
                            @endif
                        </div>
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
                        <div class="card__footer">
                            <div class="card__price-wrapper">
                                <span class="card__price">{{ $Currency::convert($product['price'], false, true) }}</span>
                            </div>
                            <button class="card__button button button--secondary" onclick="location.href = '{{ route('home.product', $product['url']) }}'">
                                <span class="icon">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#cart") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.875 17.8926C8.27344 17.8926 7.75 18.4002 7.75 19.0711C7.75 19.742 8.27344 20.2496 8.875 20.2496C9.47656 20.2496 10 19.742 10 19.0711C10 18.4002 9.47656 17.8926 8.875 17.8926ZM6.25 19.0711C6.25 17.6118 7.40549 16.3926 8.875 16.3926C10.3445 16.3926 11.5 17.6118 11.5 19.0711C11.5 20.5304 10.3445 21.7496 8.875 21.7496C7.40549 21.7496 6.25 20.5304 6.25 19.0711Z" fill="currentColor"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.375 17.8926C15.7734 17.8926 15.25 18.4002 15.25 19.0711C15.25 19.742 15.7734 20.2496 16.375 20.2496C16.9766 20.2496 17.5 19.742 17.5 19.0711C17.5 18.4002 16.9766 17.8926 16.375 17.8926ZM13.75 19.0711C13.75 17.6118 14.9055 16.3926 16.375 16.3926C17.8445 16.3926 19 17.6118 19 19.0711C19 20.5304 17.8445 21.7496 16.375 21.7496C14.9055 21.7496 13.75 20.5304 13.75 19.0711Z" fill="currentColor"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.875 3C1.875 2.58579 2.21079 2.25 2.625 2.25H4.77856C5.41521 2.25 5.95014 2.72849 6.02083 3.3612L7.04587 12.5355H18.7729C18.8813 12.5355 18.9773 12.4656 19.0107 12.3625L20.8625 6.64844C20.9149 6.48695 20.7945 6.32137 20.6247 6.32137H8.875C8.46079 6.32137 8.125 5.98558 8.125 5.57137C8.125 5.15715 8.46079 4.82137 8.875 4.82137H20.6247C21.813 4.82137 22.6558 5.98043 22.2895 7.11088L20.4376 12.825C20.2038 13.5467 19.5315 14.0355 18.7729 14.0355H6.57908L5.5483 14.6413C5.47808 14.6826 5.45059 14.7267 5.43722 14.7629C5.42143 14.8058 5.41737 14.8623 5.43374 14.9225C5.45012 14.9826 5.48226 15.0293 5.51758 15.0582C5.54748 15.0827 5.59352 15.1068 5.67497 15.1068H18.25C18.6642 15.1068 19 15.4426 19 15.8568C19 16.271 18.6642 16.6068 18.25 16.6068H5.67497C3.89172 16.6068 3.25086 14.2517 4.78824 13.3481L5.57563 12.8853L4.55494 3.75H2.625C2.21079 3.75 1.875 3.41421 1.875 3Z" fill="currentColor"/>
                                        </svg>
                                    @endif
                                </span>
                                {{ __('text.common_add_to_cart_text_d2') }}
                            </button>
                        </div>

                        @if ($product['id'] != 616)
                            <div class="card-features">
                                @if ($product['discount'] != 0)
                                    <div class="card-feature card-feature--discount">-{{ $product['discount'] }}%</div>
                                @endif
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </main>
    <aside class="aside">
        <div class="accordion aside-nav" data-accordion>
            {{-- <details class="accordion-item @if($cur_category == '') is-open @endif" data-accordion-item @if($cur_category == '') open @endif>
                <summary class="accordion-button" data-accordion-button>
                    <span class="button-text">{{__('text.common_best_selling_title')}}</span>
                    <span class="icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#paw") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 13" fill="none" width="1em" height="1em" fill="currentColor">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.36676 1.73958C6.0663 0.567387 5.14229 -0.193129 4.30228 0.0430425C3.46335 0.278131 3.02396 1.42107 3.32443 2.59327C3.62489 3.76546 4.5489 4.52489 5.38891 4.2898C6.22891 4.05472 6.66723 2.91286 6.36676 1.73958Z" fill="currentColor"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.69772 0.0430425C8.85771 -0.193129 7.9337 0.566304 7.63323 1.73958C7.33277 2.91286 7.77108 4.05363 8.61109 4.2898C9.4511 4.52598 10.3762 3.76546 10.6756 2.59327C10.975 1.42107 10.5377 0.279214 9.69772 0.0430425Z" fill="currentColor"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.8408 7.53988C12.6808 7.77605 13.6048 7.01661 13.9053 5.84334C14.2058 4.67006 13.7674 3.52929 12.9274 3.29311C12.0874 3.05694 11.1634 3.81638 10.863 4.98965C10.5636 6.16185 11.0008 7.3037 11.8408 7.53988Z" fill="currentColor"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.82278 12.1864C8.3677 12.584 9.12048 13 9.87218 13C10.696 13 11.3077 12.3749 11.3077 11.4833C11.3077 8.4499 9.04725 5.4165 7 5.4165C4.95275 5.4165 2.69227 8.4499 2.69227 11.4833C2.69227 12.3749 3.30396 13 4.12782 13C4.88059 13 5.63229 12.584 6.1783 12.1864C6.67692 11.8246 7.32416 11.8246 7.82278 12.1864Z" fill="currentColor"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.07256 3.29311C0.23255 3.5282 -0.205762 4.67006 0.0947027 5.84334C0.39409 7.01661 1.31918 7.77496 2.15918 7.53988C2.99919 7.30479 3.4375 6.16293 3.13704 4.98965C2.83657 3.81746 1.91257 3.05803 1.07256 3.29311Z" fill="currentColor"/>
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
            </details> --}}
            @foreach ($menu as $category)
                <details class="accordion-item is-open" data-accordion-item open>
                    <summary class="accordion-button" data-accordion-button>
                        <span class="button-text">{{ $category['name'] }}</span>
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#paw") }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 13" fill="none" width="1em" height="1em" fill="currentColor">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.36676 1.73958C6.0663 0.567387 5.14229 -0.193129 4.30228 0.0430425C3.46335 0.278131 3.02396 1.42107 3.32443 2.59327C3.62489 3.76546 4.5489 4.52489 5.38891 4.2898C6.22891 4.05472 6.66723 2.91286 6.36676 1.73958Z" fill="currentColor"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.69772 0.0430425C8.85771 -0.193129 7.9337 0.566304 7.63323 1.73958C7.33277 2.91286 7.77108 4.05363 8.61109 4.2898C9.4511 4.52598 10.3762 3.76546 10.6756 2.59327C10.975 1.42107 10.5377 0.279214 9.69772 0.0430425Z" fill="currentColor"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.8408 7.53988C12.6808 7.77605 13.6048 7.01661 13.9053 5.84334C14.2058 4.67006 13.7674 3.52929 12.9274 3.29311C12.0874 3.05694 11.1634 3.81638 10.863 4.98965C10.5636 6.16185 11.0008 7.3037 11.8408 7.53988Z" fill="currentColor"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.82278 12.1864C8.3677 12.584 9.12048 13 9.87218 13C10.696 13 11.3077 12.3749 11.3077 11.4833C11.3077 8.4499 9.04725 5.4165 7 5.4165C4.95275 5.4165 2.69227 8.4499 2.69227 11.4833C2.69227 12.3749 3.30396 13 4.12782 13C4.88059 13 5.63229 12.584 6.1783 12.1864C6.67692 11.8246 7.32416 11.8246 7.82278 12.1864Z" fill="currentColor"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.07256 3.29311C0.23255 3.5282 -0.205762 4.67006 0.0947027 5.84334C0.39409 7.01661 1.31918 7.77496 2.15918 7.53988C2.99919 7.30479 3.4375 6.16293 3.13704 4.98965C2.83657 3.81746 1.91257 3.05803 1.07256 3.29311Z" fill="currentColor"/>
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
        <div class="aside-cards">
            <a class="promo-card promo-card--bonus" href="{{ route('home.bonus_referral_program') }}">
                <div class="promo-card__content">
                    <div class="promo-card__title">{{ __('text.bonus_card_ref_programm') }}</div>
                    <span class="promo-card__text">{{ __('text.save_earn') }}</span>
                </div>
                <div class="promo-card__cover">
                    <img src="{{ asset("$design/svg/cards/promo-card-cover-01.svg") }}" width="288" height="211" alt="Promo card cover">
                </div>
            </a>
            <div class="promo-card promo-card--green" href="/">
                <div class="promo-card__content">
                    <div class="promo-card__title">{{ Str::ucfirst(__('text.common_banner1_text1')) }} {{ Str::ucfirst(__('text.common_banner1_text2')) }}</div>
                    <span class="promo-card__text">{{ __('text.common_banner1_text3') }} {{ __('text.common_banner1_text4') }}</span>
                </div>
                <div class="promo-card__cover">
                    <img src="{{ asset("$design/svg/cards/promo-card-cover-02.svg") }}" width="124" height="194" alt="Promo card cover">
                </div>
            </div>
            <div class="promo-card promo-card--red" href="/">
                <div class="promo-card__content">
                    <div class="promo-card__title">{{ __('text.common_banner2_text1') }} <br> <small>{!! __('text.common_banner2_text2') !!}</small></div>
                    <span class="promo-card__text">{{ __('text.common_banner2_text3') }} {{ __('text.common_banner2_text4') }}</span>
                </div>
                <div class="promo-card__cover">
                    <div class="promo-card__discount">
                        <span class="promo-card__discount-value">80%</span>
                        <span class="promo-card__discount-caption">off</span>
                    </div>
                    <img src="{{ asset("$design/svg/cards/promo-card-cover-03.svg") }}" width="200" height="171" alt="Promo card cover">
                </div>
            </div>
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