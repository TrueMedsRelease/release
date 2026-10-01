<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductPackaging;
use App\Services\SeasonalUpsellService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SeasonalUpsellController extends Controller
{
    public function index(Request $request, SeasonalUpsellService $service)
    {
        try {
            $offers = $service->offers();
        } catch (\Throwable $e) {
            report($e);
            $offers = [];
            $service->reason = 'unavailable';
        }
        $token = (string) Str::uuid();
        $packs = [];
        foreach ($offers as &$offer) {
            foreach ($offer['packs'] as &$pack) {
                $packs[$pack['pack_id']] = $pack['price_usd'];
                unset($pack['price_usd']);
            }
            unset($pack);
        }
        unset($offer);
        $request->session()->put('seasonal_upsell_offer', [
            'token' => $token, 'packs' => $packs, 'expires' => now()->addMinutes(20)->timestamp,
            'country' => strtoupper((string) session('location.country', '')),
        ]);
        return response()->json([
            'reason' => $service->reason, 'items' => $offers, 'token' => $token, 'currency' => $service->currency(),
            'max_quantity' => config('seasonal_upsell.max_quantity', 10),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function add(Request $request, SeasonalUpsellService $service)
    {
        $data = $request->validate([
            'token' => ['required', 'uuid'],
            'items' => ['present', 'array', 'max:4'],
            'items.*.pack_id' => ['required', 'integer', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:'.config('seasonal_upsell.max_quantity', 10)],
        ]);
        $completed = $request->session()->get('seasonal_upsell_completed', []);
        if (in_array($data['token'], $completed, true)) {
            return response()->json(['redirect' => route('checkout.index')]);
        }
        $offer = $request->session()->get('seasonal_upsell_offer', []);
        if (empty(session('cart')) || ($offer['token'] ?? '') !== $data['token'] || ($offer['expires'] ?? 0) < time()
            || ($offer['country'] ?? null) !== strtoupper((string) session('location.country', ''))) {
            throw ValidationException::withMessages(['items' => __('text.upsell_expired')]);
        }
        $before = $request->session()->all();
        try {
            DB::transaction(function () use ($data, $offer, $request, $service, &$completed) {
                // Serialize with refresh(): the allowed packs and API prices stay stable during this add.
                DB::table('seasonal_upsell_cache')->where('id', 1)->lockForUpdate()->first();
                $available = [];
                foreach ($service->offers() as $product) {
                    foreach ($product['packs'] as $pack) {
                        $available[$pack['pack_id']] = [
                            'product_id' => (int) $product['product_id'],
                            'price_usd' => $pack['price_usd'],
                        ];
                    }
                }
                $locked = ProductPackaging::query()->whereIn('id', array_column($data['items'], 'pack_id'))
                    ->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                $productIds = [];
                foreach ($data['items'] as $item) {
                    $id = $item['pack_id'];
                    $pack = $locked[$id] ?? null;
                    if (!isset($offer['packs'][$id], $available[$id])
                        || SeasonalUpsellService::normalizePrice($offer['packs'][$id]) !== $available[$id]['price_usd']
                        || !$pack || !$pack->is_showed || $pack->price <= 0 || $pack->num <= 0
                        || (int) $pack->product_id !== $available[$id]['product_id']
                        || in_array((int) $pack->product_id, $productIds, true)) {
                        throw ValidationException::withMessages(['items' => __('text.upsell_expired')]);
                    }
                    $productIds[] = (int) $pack->product_id;
                }
                foreach ($data['items'] as $item) {
                    for ($i = 0; $i < $item['quantity']; $i++) {
                        Cart::add($item['pack_id'], $available[$item['pack_id']]['price_usd']);
                    }
                }
                Cart::update_cart_total();
                $completed[] = $data['token'];
                $request->session()->put('seasonal_upsell_completed', array_slice($completed, -10));
            });
        } catch (\Throwable $e) {
            $request->session()->replace($before);
            throw $e;
        }
        return response()->json(['redirect' => route('checkout.index')]);
    }
}
