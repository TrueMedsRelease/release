<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PromoController extends Controller
{
    private function channelConfig(string $channel): ?array
    {
        $cfg = config("promo.channels.{$channel}");
        return (is_array($cfg) && !empty($cfg['enabled'])) ? $cfg : null;
    }

    private function promoWindowGuard(): ?\Illuminate\Http\JsonResponse
    {
        if (!session('promo_verified')) return null;

        $window = (int) config('promo.window_hours', 24) * 3600;
        $activatedAt = (int) session('promo_activated_at', 0);

        if ($activatedAt > 0 && (time() - $activatedAt) >= $window) {
            session()->forget(['promo_verified', 'promo_discount', 'promo_code', 'promo_type', 'promo_activated_at']);
            return null;
        }

        $type = session('promo_type', '');
        $message = $type
            ? __('promo.activated_channel', ['channel' => $type, 'hours' => config('promo.window_hours')])
            : __('promo.activated_session');

        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'reason'  => 'window',
        ], 400);
    }

    private function currentCustomerId(): int
    {
        return (int) ($_SESSION['user'] ?? session('customer_id', 0));
    }

    public function send(Request $request, string $channel)
    {
        if ($guard = $this->promoWindowGuard()) {
            return $guard;
        }

        if (!config('promo.promo_coupon_enabled')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Push promo is disabled',
                'reason'  => 'feature_disabled'
            ], 400);
        }

        $cfg = $this->channelConfig($channel);
        if (!$cfg) {
            return response()->json(['status' => 'error', 'message' => __('promo.unknown_channel')], 400);
        }

        $identifier = trim((string) $request->input('identifier', ''));
        if (($cfg['normalize'] ?? null) === 'lower') $identifier = strtolower($identifier);

        if (!empty($cfg['requires_identifier']) && $identifier === '') {
            return response()->json(['status' => 'error', 'message' => __('promo.fill_fields')], 400);
        }
        if (($cfg['validate'] ?? null) === 'email' && !filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['status' => 'error', 'message' => __('promo.bad_email')], 400);
        }

        $payload = [
            'type'        => $channel,
            'identifier'  => $identifier,
            'meta'        => mb_substr((string) $request->input('meta', ''), 0, 2000),
            'customer_id' => $this->currentCustomerId(),
            'session_id'  => session()->getId(),
            'ip'          => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'shop_url'    => $request->getHost(),
            'aff'         => session('aff', 0),
            'lang'        => App::currentLocale(),
            'discount_percent' => (int) config('promo.discount_percent', 5),
        ];

        try {
            $response = Http::timeout(15)->post(config('promo.true_serv.subscribe_url'), $payload);
            $result   = $response->json() ?? [];

            if ($response->successful() && ($result['status'] ?? '') === 'success') {
                session([
                    'promo_pending_channel'    => $channel,
                    'promo_pending_identifier' => $identifier,
                ]);

                return response()->json([
                    'status'  => 'success',
                    'message' => __('promo.code_sent'),
                    'code'    => $result['code']  ?? '',
                    'token'   => $result['token'] ?? '',
                ]);
            }

            Log::warning("Promo send [{$channel}] rejected by true-serv", ['result' => $result]);

            return response()->json([
                'status'  => 'error',
                'message' => $this->localizeTrueServError($result),
                'reason'  => $result['reason'] ?? null,
            ], 400);
        } catch (\Throwable $e) {
            Log::error("Promo send [{$channel}]: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => __('promo.conn_error')], 500);
        }
    }

    public function verify(Request $request, string $channel)
    {
        if ($guard = $this->promoWindowGuard()) return $guard;

        $cfg = $this->channelConfig($channel);
        if (!$cfg) {
            return response()->json(['status' => 'error', 'message' => __('promo.unknown_channel')], 400);
        }

        $code = strtoupper(trim((string) $request->input('code', '')));
        if ($code === '') {
            return response()->json(['status' => 'error', 'message' => __('promo.enter_code')], 400);
        }

        $identifier = '';
        if (!empty($cfg['verify_with_identifier'])) {
            $identifier = strtolower(trim((string) $request->input('identifier', '')));
            if ($identifier === '') {
                return response()->json(['status' => 'error', 'message' => __('promo.fill_fields')], 400);
            }

            $pendingChannel    = session('promo_pending_channel');
            $pendingIdentifier = session('promo_pending_identifier');
            if ($pendingChannel === $channel && $pendingIdentifier && $pendingIdentifier !== $identifier) {
                return response()->json([
                    'status'  => 'error',
                    'message' => __('promo.code_to_email', ['email' => $pendingIdentifier]),
                    'reason'  => 'identifier_mismatch',
                ], 400);
            }
        }

        $payload = [
            'type'       => $channel,
            'identifier' => $identifier,
            'code'       => $code,
            'session_id' => session()->getId(),
        ];

        try {
            $response = Http::timeout(15)->post(config('promo.true_serv.verify_url'), $payload);
            $result   = $response->json() ?? [];

            if ($response->successful() && ($result['status'] ?? '') === 'success') {
                session([
                    'promo_discount'     => (int) config('promo.discount_percent', 5),
                    'promo_verified'     => true,
                    'promo_code'         => $code,
                    'promo_type'         => $channel,
                    'promo_activated_at' => time(),
                ]);
                session()->forget(['promo_pending_channel', 'promo_pending_identifier']);

                return response()->json(['status' => 'success', 'message' => __('promo.activated')]);
            }

            Log::warning("Promo verify [{$channel}] rejected by true-serv", ['result' => $result]);

            return response()->json([
                'status'  => 'error',
                'message' => $this->localizeTrueServError($result),
                'reason'  => $result['reason'] ?? null,
            ], 400);
        } catch (\Throwable $e) {
            Log::error("Promo verify [{$channel}]: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => __('promo.verify_error')], 500);
        }
    }

    private function localizeTrueServError(array $result): string
    {
        $hours = (int) config('promo.window_hours', 24);
        $map = [
            'dup_used'             => __('promo.code_used'),
            'already_used'         => __('promo.code_used'),
            'dup_sent'             => __('promo.code_already_sent'),
            'expired'              => __('promo.code_expired'),
            'same_code_in_session' => __('promo.code_used_session'),
            'window'               => __('promo.activated_session'),
            'other_channel'        => __('promo.other_channel', [
                'channel' => $result['channel'] ?? '',
                'hours'   => $result['hours'] ?? $hours,
            ]),
        ];
        $key = $result['reason'] ?? null;
        return $map[$key] ?? ($result['message'] ?? __('promo.invalid_used'));
    }
}