<!DOCTYPE html>
<html lang="{{ session('locale', 'en') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">

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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset_ver('style_checkout/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('style_checkout/favicon.ico') }}">

    <style>
        .pr-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 20px;
            text-align: center;
        }
        .pr-card {
            background: #fff;
            border-radius: 20px;
            padding: 56px 44px;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 4px 24px rgba(38, 45, 56, 0.08);
        }
        .pr-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #fef2f2;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
        }
        .pr-icon svg {
            color: var(--warning-color, #ed4c54);
        }
        .pr-heading {
            font-family: 'Poppins', sans-serif;
            font-size: 23px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--main-color, #262d38);
            margin-bottom: 16px;
        }
        .pr-text {
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 400;
            line-height: 1.65;
            color: rgba(38, 45, 56, 0.7);
            margin-bottom: 32px;
        }
        .pr-btn .button {
            font-size: 16px;
            font-weight: 600;
            padding: 14px 32px;
            width: auto;
            min-width: 200px;
        }
        .pr-btn .button:hover {
            opacity: 0.85;
        }
    </style>
</head>
<body>
    <div class="pr-wrapper">
        <div class="pr-card">
            <div class="pr-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>

            <h1 class="pr-heading">{{ __('text.payment_redirect_error_heading') }}</h1>

            <p class="pr-text">
                @if ($errorMessage === 'expired')
                    {{ __('text.payment_redirect_error_expired') }}
                @else
                    {{ __('text.payment_redirect_error_invalid') }}
                @endif
            </p>

            <div class="pr-btn">
                <a href="{{ route('cart.index') }}" class="button">
                    <span>{{ __('text.payment_redirect_error_button') }}</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
