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
            <details class="accordion-item @if($cur_category == '') is-open @endif" data-accordion-item @if($cur_category == '') open @endif>
                <summary class="accordion-button" data-accordion-button>
                    <span class="button-text">{{__('text.common_best_selling_title')}}</span>
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                        </svg>
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
                <details class="accordion-item @if($cur_category == $category['name']) is-open @endif" data-accordion-item @if($cur_category == $category['name']) open @endif>
                    <summary class="accordion-button" data-accordion-button>
                        <span class="button-text">{{ $category['name'] }}</span>
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                            </svg>
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