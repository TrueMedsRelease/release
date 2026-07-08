<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class PaymentRedirectController extends Controller
{
    private const CACHE_PREFIX = 'payment_redirect:';

    private const CACHE_TTL_MINUTES = 60;

    private function resolveToken(string $token): ?array
    {
        if ($token === '') {
            Log::info('[PaymentRedirectController.resolveToken] token lookup {token_prefix: empty, found: false}');

            return null;
        }

        $tokenPrefix = substr($token, 0, 8);
        $payload = Cache::store('payment_redirects')->get(self::CACHE_PREFIX.$token);

        if ($payload === null) {
            Log::info('[PaymentRedirectController.resolveToken] token lookup {token_prefix: '.$tokenPrefix.', found: false}');

            return null;
        }

        Log::info('[PaymentRedirectController.resolveToken] token lookup {token_prefix: '.$tokenPrefix.', found: true}');

        $currentSessionId = session()->getId();

        if (! isset($payload['session_id']) || $payload['session_id'] !== $currentSessionId) {
            Log::info('[PaymentRedirectController.resolveToken] token invalid {reason: wrong_session, token_prefix: '.$tokenPrefix.'}');

            return null;
        }

        return $payload;
    }

    public function create(Request $request): JsonResponse
    {
        $type = $request->input('type', '');
        $target = $request->input('target', '');

        Log::info('[PaymentRedirectController.create] creating token {type: '.$type.'}');

        if (! in_array($type, ['url', 'form'], true)) {
            Log::info('[PaymentRedirectController.create] validation failed {errors: invalid type}');

            return response()->json(['error' => 'Invalid type'], 422);
        }

        if ($target === '') {
            Log::info('[PaymentRedirectController.create] validation failed {errors: empty target}');

            return response()->json(['error' => 'Target is required'], 422);
        }

        if ($type === 'url') {
            $parsed = parse_url($target);

            if (! isset($parsed['scheme']) || strtolower($parsed['scheme']) !== 'https') {
                Log::info('[PaymentRedirectController.create] validation failed {errors: target must be HTTPS}');

                return response()->json(['error' => 'Target URL must use HTTPS'], 422);
            }

            if (! isset($parsed['host'])) {
                Log::info('[PaymentRedirectController.create] validation failed {errors: invalid URL host}');

                return response()->json(['error' => 'Invalid target URL'], 422);
            }

            $ip = filter_var($parsed['host'], FILTER_VALIDATE_IP);
            if ($ip !== false) {
                $isInternal = filter_var(
                    $ip,
                    FILTER_VALIDATE_IP,
                    FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
                ) === false;

                if ($isInternal) {
                    Log::info('[PaymentRedirectController.create] validation failed {errors: internal IP blocked}');

                    return response()->json(['error' => 'Internal IPs are not allowed'], 422);
                }
            }
        }

        if ($type === 'form') {
            if (stripos($target, '<form') === false) {
                Log::info('[PaymentRedirectController.create] validation failed {errors: target must contain <form}');

                return response()->json(['error' => 'Invalid form HTML'], 422);
            }
        }

        $token = Str::random(64);

        Cache::store('payment_redirects')->put(self::CACHE_PREFIX.$token, [
            'type' => $type,
            'target' => $target,
            'session_id' => $request->session()->getId(),
        ], now()->addMinutes(self::CACHE_TTL_MINUTES));

        $this->cleanExpired();

        $tokenPrefix = substr($token, 0, 8);
        Log::info('[PaymentRedirectController.create] token cached {token_prefix: '.$tokenPrefix.'}');

        return response()->json([
            'redirect_url' => route('payment.redirect.show', ['token' => $token]),
        ]);
    }

    public function show(Request $request)
    {
        $token = $request->query('token', '');
        $payload = $this->resolveToken($token);

        if ($payload === null) {
            Log::info('[PaymentRedirectController.show] token invalid {reason: not_found_or_wrong_session}');

            return view('payment_redirect_error', [
                'errorMessage' => 'not_found',
                'design' => session('design', config('app.design')),
            ]);
        }

        $tokenPrefix = substr($token, 0, 8);
        Log::info('[PaymentRedirectController.show] showing intermediate page {token_prefix: '.$tokenPrefix.'}');

        return view('payment_redirect', [
            'goUrl' => route('payment.redirect.go', ['token' => $token]),
            'design' => session('design', config('app.design')),
        ]);
    }

    public function go(Request $request)
    {
        $token = $request->query('token', '');
        $payload = $this->resolveToken($token);

        if ($payload === null) {
            Log::info('[PaymentRedirectController.go] token invalid {reason: not_found_or_wrong_session}');

            return view('payment_redirect_error', [
                'errorMessage' => 'not_found',
                'design' => session('design', config('app.design')),
            ]);
        }

        $tokenPrefix = substr($token, 0, 8);
        Log::info('[PaymentRedirectController.go] performing redirect {type: '.$payload['type'].', token_prefix: '.$tokenPrefix.'}');

        if ($payload['type'] === 'url') {
            return redirect()->away($payload['target']);
        }

        return view('payment_redirect_form', [
            'formHtml' => $payload['target'],
        ]);
    }

    private function cleanExpired(): void
    {
        $store = Cache::store('payment_redirects');
        $driver = $store->getStore();

        $reflection = new \ReflectionClass($driver);
        $directory = $reflection->getProperty('directory')->getValue($driver);

        if (! is_dir($directory)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $cleaned = 0;
        foreach ($iterator as $file) {
            if ($cleaned >= 50) {
                break;
            }

            $contents = file_get_contents($file->getRealPath());

            if ($contents === false || strlen($contents) < 10) {
                continue;
            }

            $expiresAt = (int) substr($contents, 0, 10);

            if ($expiresAt < time()) {
                unlink($file->getRealPath());
                $cleaned++;
            }
        }

        if ($cleaned > 0) {
            Log::info('[PaymentRedirectController.cleanExpired] cleaned {count} expired entries', ['count' => $cleaned]);
        }
    }
}
