@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
    <div class="christmas" style="display: none" onclick="location.href='{{ route('home.checkup') }}'">
        <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_big.png") }}">
    </div>

    <div class="text-page mb50">
        <h2 class="title-page">{{__('text.menu_title_sitemap')}}</h2>
        <div style="margin-bottom: 20px">
            <div style="font-size: 20px; margin-bottom: 20px;">{{ __('text.sitemap_site_info') }}:</div>
            <div class="site_info_url_block" style="margin-left: 20px">
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
            <div style="font-size: 20px; margin-bottom: 20px;">{{ __('text.category_title') }}:</div>
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 20px;">
                <div style="margin-left: 20px">
                    <div>
                        <div class="site_info_url_item" >
                            <a class="site_info_link" href="{{ route('home.index') }}" style="font-size: 18px">
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
                                <a class="site_info_link" href="{{ route('home.category', $category['url']) }}" style="font-size: 18px">
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
            <div style="font-size: 20px; margin-bottom: 20px;">{{ __('text.sitemap_language_title') }}:</div>
            <div class="site_info_url_block">
                @foreach ($Language::GetAllLanuages() as $item)
                    <div class="site_info_url_item">
                        <a class="site_info_link" href="{{ route('home.language', $item['code']) }}">{{ $item['name'] }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="sale-banners">
        <div class="happy-sale item">
            <span class="img">
                <img loading="lazy" src="{{ asset("$design/images/icon/ico-banner-01.svg") }}" alt="">
            </span>
            <span class="info">
                <span class="title">{{__('text.common_banner1_text1')}} <br>{{__('text.common_banner1_text2')}}</span>
                <span class="text">{{__('text.common_banner1_text3')}} <br> {{__('text.common_banner1_text4')}}</span>
            </span>
        </div>
        <div class="wow-sale item">
            <span class="img">
                <img loading="lazy" src="{{ asset("$design/images/icon/ico-banner-02.svg") }}" alt="">
            </span>
            <span class="info">
                <span class="title">{{__('text.common_banner2_text1')}} <br> {!!__('text.common_banner2_text2')!!}</span>
                <span class="text">{{__('text.common_banner2_text3')}} <br> {{__('text.common_banner2_text4')}}</span>
            </span>
        </div>
    </div>
@endsection