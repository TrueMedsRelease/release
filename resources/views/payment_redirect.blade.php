<!DOCTYPE html>
<html lang="{{ session('locale', 'en') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="refresh" content="5;url={{ $goUrl }}">

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
        .pr-loader {
            position: relative;
            width: 72px;
            height: 72px;
            margin: 0 auto 32px;
        }
        .pr-loader-ring {
            position: absolute;
            inset: 0;
            border: 3px solid #e8eaed;
            border-radius: 50%;
        }
        .pr-loader-spin {
            position: absolute;
            inset: 0;
            border: 3px solid transparent;
            border-top-color: var(--accent-color, #508111);
            border-radius: 50%;
            animation: pr-spin 0.75s cubic-bezier(0.4, 0.0, 0.2, 1) infinite;
        }
        .pr-loader-check {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 28px;
            height: 28px;
        }
        @keyframes pr-spin {
            to { transform: rotate(360deg); }
        }
        .pr-heading {
            font-family: 'Poppins', sans-serif;
            font-size: 23px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--main-color, #262d38);
            margin-bottom: 20px;
        }
        .pr-body {
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 400;
            line-height: 1.65;
            color: rgba(38, 45, 56, 0.7);
        }
        .pr-body p {
            margin-bottom: 6px;
        }
        .pr-body p:last-child {
            margin-bottom: 0;
        }
        .pr-notice {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            text-align: left;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            line-height: 1.55;
            color: rgba(38, 45, 56, 0.6);
            background: #f4f5f6;
            border-radius: 12px;
            padding: 14px 16px;
            margin: 28px 0 0;
        }
        .pr-notice svg {
            flex-shrink: 0;
            margin-top: 2px;
        }
        .pr-btn {
            display: none;
            margin-top: 8px;
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
        .pr-progress {
            margin-top: 28px;
            width: 100%;
            max-width: 200px;
            height: 3px;
            background: #e8eaed;
            border-radius: 2px;
            overflow: hidden;
        }
        .pr-progress-bar {
            height: 100%;
            width: 0;
            background: var(--accent-color, #508111);
            border-radius: 2px;
            animation: pr-progress 5s linear forwards;
        }
        @keyframes pr-progress {
            to { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="pr-wrapper">
        <div class="pr-card">
            <div class="pr-loader">
                <div class="pr-loader-ring"></div>
                <div class="pr-loader-spin"></div>
                <svg class="pr-loader-check" viewBox="0 0 24 24" fill="none" stroke="var(--accent-color, #508111)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>

            <h1 class="pr-heading">{{ __('text.payment_redirect_heading') }}</h1>
            <div class="pr-body">
                <p>{{ __('text.payment_redirect_text_1') }}</p>
                <p>{{ __('text.payment_redirect_text_2') }}</p>
            </div>

            <div class="pr-notice">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(38,45,56,0.45)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="12" x2="12" y2="16"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <span>{{ __('text.payment_redirect_warning') }}</span>
            </div>

            <div class="pr-progress">
                <div class="pr-progress-bar"></div>
            </div>

            <div class="pr-btn" id="manual-btn">
                <a href="{{ $goUrl }}" class="button">
                    <span>{{ __('text.payment_redirect_button') }}</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function () {
            var btn = document.getElementById('manual-btn');
            if (btn) {
                btn.style.display = 'block';
            }
        }, 6000);
    </script>
</body>
</html>
