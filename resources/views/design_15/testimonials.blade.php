@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)
@section('header_class', 'header--secondary')
@section('delivery_style', 'margin: 2.2rem 0;')
@section('delivery_style_2', 'padding-bottom: 0;')

@section('content')
<div class="page-wrapper container">
    <main class="main">
        <div class="content">
            <h1>{{__('text.testimonials_title')}}</h1>
            <div class="testimonials">
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
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_2')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_2')}}</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_3')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_3')}}</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_4')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_4')}}</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_5')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_5')}}</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_6')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_6')}}</div>
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
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_8')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_8')}}</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_9')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_9')}}</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_10')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_10')}}</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_11')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_11')}}</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_12')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_12')}}</div>
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
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_14')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_14')}}</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_15')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_15')}}</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_16')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_16')}}</div>
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
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_18')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_18')}}</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_19')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_19')}}</div>
                </div>
                <div class="testimonial">
                    <div class="testimonial__header">
                        <div class="testimonial__author">{!!__('text.testimonials_author_t_20')!!}</div>
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
                    <div class="testimonial__text">{{__('text.testimonials_t_20')}}</div>
                </div>
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
            {{-- <a class="promo-card promo-card--bonus" href="/">
                <div class="promo-card__content">
                    <div class="promo-card__title">Bonus Card & <br> Referral Program</div>
                    <span class="promo-card__text">Save & Earn</span>
                </div>
                <div class="promo-card__cover">
                    <img src="{{ asset("$design/svg/cards/promo-card-cover-01.svg") }}" width="288" height="211" alt="Promo card cover">
                </div>
            </a> --}}
            <a class="promo-card promo-card--green" href="/">
                <div class="promo-card__content">
                    <div class="promo-card__title">{{ Str::ucfirst(__('text.common_banner1_text1')) }} {{ Str::ucfirst(__('text.common_banner1_text2')) }}</div>
                    <span class="promo-card__text">{{ __('text.common_banner1_text3') }} {{ __('text.common_banner1_text4') }}</span>
                </div>
                <div class="promo-card__cover">
                    <img src="{{ asset("$design/svg/cards/promo-card-cover-02.svg") }}" width="124" height="194" alt="Promo card cover">
                </div>
            </a>
            <a class="promo-card promo-card--red" href="/">
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
            </a>
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