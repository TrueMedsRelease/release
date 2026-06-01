<!doctype html>
<html
@if (session('locale', 'en') == 'arb')
    lang="ar"
@elseif (session('locale', 'en') == 'gr')
    lang="el"
@elseif (session('locale', 'en') == 'hans')
    lang="zh-Hans"
@elseif (session('locale', 'en') == 'hant')
    lang="zh-Hant"
@elseif (session('locale', 'en') == 'no')
    lang="nb"
@else
    lang="{{ session('locale', 'en') }}"
@endif
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Install App</title>

        @if (env('APP_DEFAULT_META', 1))
            <meta name="Description" content="@yield('description', 'Description')">
            <meta name="Keywords" content="@yield('keywords', 'Keywords')">
        @else
            <meta name="Description" content="Description">
            <meta name="Keywords" content="Keywords">
        @endif

        <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
        <link rel="canonical" href="{{ url()->current() }}">

        @php
            if (!function_exists('asset_ver')) {
                function asset_ver(string $path): string {
                    static $mtimes = [];
                    $full = public_path($path);
                    if (!isset($mtimes[$path])) {
                        $mtimes[$path] = is_file($full) ? filemtime($full) : null;
                    }
                    $url = asset($path);
                    $v = $mtimes[$path] ?? time();
                    return $url . '?v=' . $v;
                }
            }
        @endphp

        @foreach ($Language::GetAllLanuages() as $item)
            <link rel="alternate" href="{{ route('home.language', $item['code']) }}"
                @if ($item['code'] == 'arb')
                    hreflang="ar"
                @elseif ($item['code'] == 'gr')
                    hreflang="el"
                @elseif ($item['code'] == 'hans')
                    hreflang="zh-Hans"
                @elseif ($item['code'] == 'hant')
                    hreflang="zh-Hant"
                @else
                    hreflang={{ $item['code'] }}
                @endif
            />
        @endforeach

        <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.ico') }}" sizes="any">
        <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">
        <script src="{{ asset("vendor/jquery/jquery-3.6.3.min.js") }}"></script>

        <script>
            function isPwaMode() {
                return window.matchMedia('(display-mode: standalone)').matches ||
                    window.matchMedia('(display-mode: fullscreen)').matches ||
                    window.navigator.standalone === true;
            }

            function setPwaModeCookie() {
                const isPwa = isPwaMode() ? '1' : '0';

                document.cookie =
                    'is_pwa=' + isPwa +
                    '; path=/' +
                    '; max-age=31536000' +
                    '; SameSite=Lax';
            }

            setPwaModeCookie();

            window.matchMedia('(display-mode: standalone)').addEventListener?.('change', setPwaModeCookie);
            window.matchMedia('(display-mode: fullscreen)').addEventListener?.('change', setPwaModeCookie);
        </script>

        <link href="{{ asset_ver('/style_install_page/style.css') }}" rel="stylesheet">
    </head>
    <body>
        @php
            $appName = 'True Pharmacy';
            $appShortDescription = __('text.install_page_desc');

            $screens = [
                ['src' => $design . '/images/favicon/screen_1.webp', 'caption' => __('text.install_page_desc_img1')],
                ['src' => $design . '/images/favicon/screen_2.webp', 'caption' => __('text.install_page_desc_img2')],
                ['src' => $design . '/images/favicon/screen_3.webp', 'caption' => __('text.install_page_desc_img3')],
                ['src' => $design . '/images/scrin.png', 'caption' => __('text.install_page_desc_img4')],
            ];

            $exampleReviews = [
                ['stars' => '★★★★★', 'text' => __('text.testimonials_t_1')],
                ['stars' => '★★★★★', 'text' => __('text.testimonials_t_7')],
                ['stars' => '★★★★★', 'text' => __('text.testimonials_t_8')],
                ['stars' => '★★★★★', 'text' => __('text.testimonials_t_17')],
            ];
        @endphp

        <div class="pwa-page">
            <div class="pwa-card">
                <section class="pwa-hero">
                    <div>
                        <div class="pwa-main-info">
                            <div class="pwa-icon">
                                <img src="{{ asset($design . '/images/favicon/android-chrome-192x192.png') }}" alt="{{ $appName }}">
                            </div>

                            <div>
                                <h1 class="pwa-title">{{ $appName }}</h1>
                                <p class="pwa-subtitle">{{ $appShortDescription }}</p>

                                <div class="pwa-meta">
                                    <div class="pwa-meta-item">
                                        <strong>5.0</strong>
                                        <span>{{ __('text.install_page_rating') }}</span>
                                    </div>
                                    <div class="pwa-meta-item">
                                        <strong>PWA</strong>
                                        <span>{{ __('text.install_page_app_format') }}</span>
                                    </div>
                                    <div class="pwa-meta-item">
                                        <strong>24/7</strong>
                                        <span>{{ __('text.install_page_quick_access') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pwa-actions">
                        <div id="pwa-install-block">
                            <button id="pwa-install-button" class="pwa-button pwa-button-primary" type="button" disabled>
                                {{ __('text.install_page_install_app') }}
                            </button>

                            <div id="pwa-install-status" class="pwa-status">
                                {{ __('text.install_page_check_install') }}
                            </div>
                        </div>

                        <div id="pwa-installed-block" class="pwa-installed-block">
                            <p><strong>{{ __('text.install_page_already_install') }}</strong></p>
                            {{-- <p>On desktop, Chrome may also show “Open in App” in the address bar.</p> --}}

                            <button id="pwa-open-button" class="pwa-button pwa-button-secondary" type="button">
                                {{ __('text.install_page_open_app') }}
                            </button>
                        </div>

                        <a href="{{ route('home.index') }}" class="pwa-button pwa-button-back">
                            {{ __('text.install_page_back_to_shop') }}
                        </a>
                    </div>
                </section>

                <main class="pwa-content">
                    <section class="pwa-section">
                        <h2 class="pwa-section-title">{{ __('text.install_page_screenshots') }}</h2>

                        <div class="pwa-screens-wrapper">
                            <div class="pwa-screens-scroll">
                                @foreach($screens as $screen)
                                    <div class="pwa-screen">
                                        <div class="pwa-screen-image">
                                            <img src="{{ asset($screen['src']) }}" alt="{{ $screen['caption'] }}">
                                        </div>

                                        <div class="pwa-screen-caption">
                                            {{ $screen['caption'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <section class="pwa-section pwa-grid">
                        <div>
                            <h2 class="pwa-section-title">{{ __('text.install_page_about_app') }}</h2>

                            <p class="pwa-about">
                                {{ __('text.install_page_about_text') }}
                            </p>

                            <div class="pwa-features">
                                <div class="pwa-feature">✓ {{ __('text.install_page_about_desc1') }}</div>
                                <div class="pwa-feature">✓ {{ __('text.install_page_about_desc2') }}</div>
                                <div class="pwa-feature">✓ {{ __('text.install_page_about_desc3') }}</div>
                                <div class="pwa-feature">✓ {{ __('text.install_page_about_desc4') }}</div>
                            </div>
                        </div>

                        <div>
                            <h2 class="pwa-section-title">{{ __('text.install_page_reviews') }}</h2>

                            <div class="pwa-reviews">
                                @foreach($exampleReviews as $review)
                                    <div class="pwa-review">
                                        <div class="pwa-review-stars">{{ $review['stars'] }}</div>
                                        <p class="pwa-review-text">{{ $review['text'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </main>
            </div>
            <div class="footer__copyrights">
                {{ __('text.license_text_license1_1') }}
                {{ $domain }}
                {{ __('text.license_text_license1_2') }}
                {{ __('text.license_text_license2_d13') }}
            </div>
        </div>

        <script>
            const routePwaInstallEvent = "{{ route('home.pwa_install_event') }}";
        </script>

        <script defer type="text/javascript" src="{{ asset_ver("vendor/jquery/pwa.js") }}"></script>

    </body>
</html>