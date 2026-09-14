<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PromoController extends Controller
{
    /** Конфиг канала; null = канал неизвестен или отключён (вся проверка только здесь) */
    private function channelConfig(string $channel): ?array
    {
        $cfg = config("promo.channels.{$channel}");
        return (is_array($cfg) && !empty($cfg['enabled'])) ? $cfg : null;
    }

    /** Запрет повторной активации другим каналом в окне N часов в одной сессии */
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
            ? "Скидка уже активирована через канал «{$type}». Повторная активация доступна через " . config('promo.window_hours', 24) . " ч."
            : 'Скидка уже активирована в этой сессии.';

        return response()->json(['status' => 'error', 'message' => $message], 400);
    }

    /** УНИВЕРСАЛЬНАЯ отправка кода: /promo/send/{channel} */
    public function send(Request $request, string $channel)
    {
        if ($guard = $this->promoWindowGuard()) {
            return $guard;
        }

        $cfg = $this->channelConfig($channel);
        if (!$cfg) {
            return response()->json(['status' => 'error', 'message' => 'Неизвестный канал подписки'], 400);
        }

        $identifier = trim((string) $request->input('identifier', ''));
        if (($cfg['normalize'] ?? null) === 'lower') $identifier = strtolower($identifier);

        if (!empty($cfg['requires_identifier']) && $identifier === '') {
            return response()->json(['status' => 'error', 'message' => 'Заполните все поля'], 400);
        }
        if (($cfg['validate'] ?? null) === 'email' && !filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['status' => 'error', 'message' => 'Некорректный email'], 400);
        }

        $payload = [
            'type'        => $channel,
            'identifier'  => $identifier,
            'meta'        => mb_substr((string) $request->input('meta', ''), 0, 2000), // любые доп. данные канала (JSON)
            'customer_id' => (int) session('customer_id', 0),
            'session_id'  => session()->getId(),
            'ip'          => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'shop_url'    => $request->getHost(),
            'aff'         => session('aff', 0),
        ];

        try {
            $response = Http::timeout(15)->post(config('promo.true_serv.subscribe_url'), $payload);
            $result   = $response->json();

            if ($response->successful() && ($result['status'] ?? '') === 'success') {
                return response()->json([
                    'status'  => 'success',
                    'message' => $result['message'] ?? 'Код отправлен',
                    'code'    => $result['code']  ?? '',
                    'token'   => $result['token'] ?? '',
                ]);
            }
            return response()->json(['status' => 'error', 'message' => $result['message'] ?? 'Ошибка отправки'], 400);
        } catch (\Throwable $e) {
            Log::error("Promo send [{$channel}]: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Ошибка соединения с сервером'], 500);
        }
    }

    /** УНИВЕРСАЛЬНАЯ проверка кода: /promo/verify/{channel} */
    public function verify(Request $request, string $channel)
    {
        if ($guard = $this->promoWindowGuard()) return $guard;

        $cfg = $this->channelConfig($channel);
        if (!$cfg) {
            return response()->json(['status' => 'error', 'message' => 'Неизвестный канал подписки'], 400);
        }

        $code = strtoupper(trim((string) $request->input('code', '')));
        if ($code === '') {
            return response()->json(['status' => 'error', 'message' => 'Введите код'], 400);
        }

        $identifier = '';
        if (!empty($cfg['verify_with_identifier'])) {
            $identifier = strtolower(trim((string) $request->input('identifier', '')));
            if ($identifier === '') {
                return response()->json(['status' => 'error', 'message' => 'Заполните все поля'], 400);
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
            $result   = $response->json();

            if ($response->successful() && ($result['status'] ?? '') === 'success') {
                session([
                    'promo_discount'     => (int) config('promo.discount_percent', 5),
                    'promo_verified'     => true,
                    'promo_code'         => $code,
                    'promo_type'         => $channel,
                    'promo_activated_at' => time(),
                ]);
                return response()->json(['status' => 'success', 'message' => 'Скидка активирована!']);
            }
            return response()->json(['status' => 'error', 'message' => $result['message'] ?? 'Неверный или уже использованный код'], 400);
        } catch (\Throwable $e) {
            Log::error("Promo verify [{$channel}]: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Ошибка проверки кода'], 500);
        }
    }
}