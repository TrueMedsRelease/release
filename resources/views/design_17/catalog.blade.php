@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
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

        $selectedCategoryName = __('text.common_all_products');
        foreach ($menu as $menuCategory) {
            if ($menuCategory['url'] === $category) {
                $selectedCategoryName = $menuCategory['name'];
                break;
            }
        }
    @endphp
    <script>
        window.catalogConfig = {
            loadUrl: "{{ route('catalog.load') }}",
            has_more: {{ $has_more ? 'true' : 'false' }},
            next_cursor: @json($next_cursor),
            search: @json($search),
            category: @json($category)
        };
    </script>

    <div class="thread-chat js-chat-thread-wrap">
        <div class="thread-chat__container">
            <div class="thread-chat__messages js-chat-thread">
                <div class="catalog js-catalog">
                    <aside class="catalog__sidebar">
                        <div class="catalog__sidebar-search js-catalog-category-filter">
                            <svg class="catalog__sidebar-search-icon" width="1em" height="1em" fill="currentColor" aria-hidden="true">
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#history') }}"></use>
                            </svg>
                            <input class="catalog__sidebar-search-input js-catalog-category-filter-input"
                                   type="search"
                                   placeholder="{{ __('text.common_search_categories') }}"
                                   aria-label="{{ __('text.common_search_categories') }}"
                                   autocomplete="off">
                            <button class="catalog__sidebar-search-reset js-catalog-category-reset"
                                    type="button"
                                    aria-label="Reset search"
                                    hidden>
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#close') }}"></use>
                                </svg>
                            </button>
                        </div>

                        <nav class="catalog__menu js-catalog-menu" aria-label="{{ __('text.common_catalog_categories') }}">
                            <div class="catalog__menu-body js-catalog-menu-body">
                                <button class="catalog__menu-item js-catalog-category {{ $category === '' ? 'is-active' : '' }}"
                                        type="button"
                                        data-category="">
                                    {{ __('text.common_all_products') }}
                                </button>
                                @foreach ($menu as $menuCategory)
                                    <button class="catalog__menu-item js-catalog-category {{ $menuCategory['url'] === $category ? 'is-active' : '' }}"
                                            type="button"
                                            data-category="{{ $menuCategory['url'] }}">
                                        {{ $menuCategory['name'] }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="catalog__menu-empty js-catalog-category-empty" hidden>
                                {{ __('text.common_search_no_results') }}
                            </div>
                        </nav>
                    </aside>

                    <div class="catalog__main">
                        <a class="catalog__back js-catalog-back" href="{{ route('home.index') }}">
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#arrow-left') }}"></use>
                                </svg>
                            </span>
                            {{ __('text.common_back_to_chat') }}
                        </a>
                        <div class="catalog__heading">
                            <div class="catalog__heading-left">
                                <h1 class="catalog__title js-catalog-title">{{ $selectedCategoryName }}</h1>
                                <span class="catalog__count js-catalog-count" data-label="{{ __('text.common_product_text') }}">{{ $total }} {{ __('text.common_product_text') }}</span>
                            </div>
                            <form class="catalog__search js-catalog-search-form" autocomplete="off">
                                <input class="catalog__search-input js-catalog-search-input"
                                       type="search"
                                       name="search"
                                       placeholder="{{ __('text.common_search_medbot') }}"
                                       value="{{ $search }}"
                                       aria-label="{{ __('text.common_search_medbot') }}">
                                <button class="catalog__search-submit button" type="submit" aria-label="Search">
                                    <span class="icon">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#arrow-right') }}"></use>
                                        </svg>
                                    </span>
                                </button>
                            </form>
                        </div>

                        <div class="catalog__grid js-catalog-grid">
                            <div class="cards">
                                @include($design . '.ajax.catalog_cards', [
                                    'products' => $products,
                                    'Currency' => $Currency,
                                ])
                            </div>
                        </div>

                        <div class="catalog__loader js-catalog-loader" hidden>
                            <div class="dc17-chat-loader__spinner"></div>
                        </div>

                        <div class="catalog__sentinel js-catalog-sentinel"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script defer src="{{ asset_ver($design . '/js/catalog.js') }}"></script>
@endsection
