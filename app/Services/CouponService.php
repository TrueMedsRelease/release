<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Coupon in the session.
 *
 *   session('coupon_get') — raw code from ?coupon=..., not validated yet;
 *   session('coupon')     — validated by the shop API: ['coupon', 'percent', 'type'].
 *
 * The coupon is validated as soon as the customer lands (SessionParameterHandler),
 * so storefront pages can show real numbers instead of an unconfirmed promise.
 */
class CouponService
{
    /** Product that never gets a coupon discount (see Cart::update_cart_total()). */
    public const EXCLUDED_PRODUCT_ID = 616;

    /**
     * Validate a pending ?coupon= code against the shop API and store it as session('coupon').
     * Keeps an already validated coupon, same as Cart::update_cart_total() did before.
     */
    public static function resolvePending(int $timeout = 20): void
    {
        $code = session('coupon_get');

        if (empty($code) || !is_string($code)) {
            return;
        }

        if (session()->has('coupon')) {
            session()->forget('coupon_get');
            return;
        }

        if (!TrueServService::available()) {
            // Leave it pending: Cart::update_cart_total() will try again later.
            return;
        }

        try {
            $response = Http::timeout($timeout)->post('http://true-serv.net/checkout/order.php', [
                'method'  => 'coupon',
                'api_key' => DB::table('shop_keys')->where('name_key', '=', 'api_key')->value('key_data'),
                'coupon'  => $code,
            ]);
            Log::info("Cart coupon answer: " . $response);

            if ($response->successful()) {
                $data = json_decode($response, true);

                if (is_array($data) && ($data['status'] ?? null) == 'success') {
                    session(['coupon' => [
                        'coupon'  => $code,
                        'percent' => $data['coupon']['percent'],
                        'type'    => $data['coupon']['type'],
                    ]]);
                }
            }
        } catch (ConnectionException $e) {
            // Timed out / unreachable: keep it pending, the cart will retry.
            Log::error("Coupon check connection error: " . $e->getMessage());
            return;
        } catch (RequestException $e) {
            Log::error("Coupon check request error: " . $e->getMessage());
        }

        session()->forget('coupon_get');
    }

    /** Validated coupon percent, 0 when there is none. */
    public static function percent(): float
    {
        return session()->has('coupon') ? max(0.0, (float) session('coupon.percent', 0)) : 0.0;
    }

    /**
     * Will the coupon be applied at checkout? Not when the customer pays with a
     * gift/bonus card instead (checked_bonus), same rule as Cart::update_cart_total().
     */
    public static function active(): bool
    {
        return self::percent() > 0 && session('checked_bonus', 'discount') == 'discount';
    }

    /** Coupon discount for a product price, rounded like the cart does. */
    public static function discount(float $price, $productId = null): float
    {
        if (!self::active() || (int) $productId === self::EXCLUDED_PRODUCT_ID) {
            return 0.0;
        }

        return ceil($price * (self::percent() / 100));
    }

    /** "10" / "7.5" — percent for display. */
    public static function percentLabel(): string
    {
        return rtrim(rtrim(number_format(self::percent(), 2, '.', ''), '0'), '.');
    }
}
