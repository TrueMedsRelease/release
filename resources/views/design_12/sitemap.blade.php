@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)
@section('header_class', 'header--secondary')

@section('content')
<div class="page-wrapper container">
    <main class="main">
        <article class="content content--page">
            <h1>{{__('text.menu_title_sitemap')}}</h1>
            <div style="margin-bottom: 20px">
                <div style="font-size: 2.5rem; margin-bottom: 20px;">{{ __('text.sitemap_site_info') }}:</div>
                <div class="site_info_url_block">
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        @php
                            $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
                        @endphp
                        <div class="site_info_url_item"><a class="site_info_link" href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a></div>
                        <div class="site_info_url_item"><a class="site_info_link" href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a></div>
                        <div class="site_info_url_item"><a class="site_info_link" href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a></div>
                        <div class="site_info_url_item"><a class="site_info_link" href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a></div>
                        <div class="site_info_url_item"><a class="site_info_link" href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a> </div>
                        <div class="site_info_url_item"><a class="site_info_link" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a></div>
                    @else
                        <div class="site_info_url_item"><a class="site_info_link" href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a></div>
                        <div class="site_info_url_item"><a class="site_info_link" href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a></div>
                        <div class="site_info_url_item"><a class="site_info_link" href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a></div>
                        <div class="site_info_url_item"><a class="site_info_link" href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a></div>
                        <div class="site_info_url_item"><a class="site_info_link" href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a> </div>
                        <div class="site_info_url_item"><a class="site_info_link" href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a></div>
                    @endif
                </div>
            </div>
            <div style="margin-bottom: 20px">
                <div style="font-size: 2.5rem; margin-bottom: 20px;">{{ __('text.category_title') }}:</div>
                <div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 20px;">
                    <div style="margin-left: 20px">
                        <div>
                            <div class="site_info_url_item" >
                                <a class="site_info_link" href="{{ route('home.index') }}" style="font-size: 2rem">
                                    {{ __('text.common_best_selling_title') }}
                                </a>
                            </div>
                        </div>
                        <div style="margin-left: 20px">
                            @foreach ($bestsellers as $bestseller)
                                <div class="site_info_url_item">
                                    <a class="site_info_link" href="{{ route('home.product', $bestseller['url']) }}">
                                        {{ $bestseller['name'] }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @foreach ($menu as $category)
                        <div style="margin-left: 20px">
                            <div>
                                <div class="site_info_url_item">
                                    <a class="site_info_link" href="{{ route('home.category', $category['url']) }}" style="font-size: 2rem">
                                        {{ $category['name'] }}
                                    </a>
                                </div>
                            </div>
                            <div style="margin-left: 20px">
                                @foreach ($category['products'] as $item)
                                    <div class="site_info_url_item">
                                        <a class="site_info_link" href="{{ route('home.product', $item['url']) }}">{{ $item['name'] }}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div style="margin-bottom: 20px">
                <div style="font-size: 2.5rem; margin-bottom: 20px;">{{ __('text.sitemap_language_title') }}:</div>
                <div class="site_info_url_block">
                    @foreach ($Language::GetAllLanuages() as $item)
                        <div class="site_info_url_item">
                            <a class="site_info_link" href="{{ route('home.language', $item['code']) }}">{{ $item['name'] }}</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    </main>
</div>
@endsection

@section('rewies')
<div class="container">
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
</div>
@endsection