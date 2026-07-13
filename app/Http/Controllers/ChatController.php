<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductDesc;
use App\Models\ProductPackaging;
use App\Services\MedicalAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    private MedicalAssistantService $medicalAssistant;

    public function __construct()
    {
        $this->medicalAssistant = app(MedicalAssistantService::class);
        Log::debug('[ChatController.__construct] initialized');
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:1|max:512',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()->toArray(),
            ], 422);
        }

        $message = trim($request->input('message'));
        $language = session('language') ?? config('app.language', 'en');
        $localId = (string) Str::uuid();

        Log::info('[ChatController.sendMessage] new query', [
            'query' => $message,
            'language' => $language,
            'local_id' => $localId,
            'ip' => $request->ip(),
        ]);

        $this->appendToHistory('user', $message);
        session()->save();

        Cache::put("chat_pending:{$localId}", [
            'query' => $message,
            'language' => $language,
            'created_at' => now()->toIso8601String(),
        ], 300);

        return response()->json([
            'success' => true,
            'message_id' => $localId,
            'status' => 'queued',
        ]);
    }

    public function pollMessage(string $messageId): JsonResponse
    {
        Log::debug('[ChatController.pollMessage] polling', [
            'msg_id' => $messageId,
        ]);

        $pendingKey = "chat_pending:{$messageId}";
        $pending = Cache::get($pendingKey);

        if ($pending && empty($pending['resolving'])) {
            $pending['resolving'] = true;
            Cache::put($pendingKey, $pending, 120);

            try {
                $result = $this->medicalAssistant->sendQuery(
                    $pending['query'],
                    $pending['language']
                );
            } catch (\Exception $e) {
                Log::error('[ChatController.pollMessage] sendQuery exception', [
                    'local_id' => $messageId,
                    'error' => $e->getMessage(),
                ]);
                $pending['resolving'] = false;
                Cache::put($pendingKey, $pending, 120);
                return response()->json([
                    'success' => true,
                    'status' => 'queued',
                ]);
            }

            if (!$result['ok']) {
                Log::error('[ChatController.pollMessage] sendQuery failed', [
                    'local_id' => $messageId,
                    'error' => $result['error'],
                ]);
                $pending['resolving'] = false;
                Cache::put($pendingKey, $pending, 120);
                return response()->json([
                    'success' => true,
                    'status' => 'queued',
                ]);
            }

            Cache::forget($pendingKey);

            $realId = $result['data']['id'] ?? null;
            if (!$realId) {
                Log::error('[ChatController.pollMessage] no id in response', [
                    'local_id' => $messageId,
                ]);
                return response()->json([
                    'success' => true,
                    'status' => 'queued',
                ]);
            }

            Cache::put("chat_real_id:{$messageId}", $realId, 300);

            Log::info('[ChatController.pollMessage] resolved local→real', [
                'local_id' => $messageId,
                'real_id' => $realId,
            ]);

            return response()->json([
                'success' => true,
                'status' => 'queued',
            ]);
        }

        if ($pending && !empty($pending['resolving'])) {
            return response()->json([
                'success' => true,
                'status' => 'queued',
            ]);
        }

        $realId = Cache::get("chat_real_id:{$messageId}");
        if ($realId) {
            $messageId = $realId;
        } else {
            Log::warning('[ChatController.pollMessage] no real_id for message', [
                'local_id' => $messageId,
            ]);
            return response()->json([
                'success' => false,
                'message' => __('text.chat_error_message_not_found'),
            ]);
        }

        $result = $this->medicalAssistant->pollStatus($messageId);

        if (!$result['ok']) {
            return $this->buildErrorResponse($result['error'], $result['http_status']);
        }

        $data = $result['data'];
        $status = $data['status'] ?? 'unknown';
        $response = [
            'success' => true,
            'status' => $status,
            'steps' => $data['steps'] ?? [],
            'created_at' => $data['created_at'] ?? null,
            'completed_at' => $data['completed_at'] ?? null,
        ];

        Log::debug('[ChatController.pollMessage] status', [
            'msg_id' => $messageId,
            'status' => $status,
        ]);

        if ($status === 'done' && !empty($data['result'])) {
            $apiResult = $data['result'];
            $response['answer'] = $apiResult['answer'] ?? '';
            $response['language'] = $apiResult['language'] ?? 'en';
            $response['product_ids'] = $apiResult['product_ids'] ?? [];

            $productIds = $apiResult['product_ids'] ?? [];

            if (!empty($productIds)) {
                Log::debug('[ChatController.pollMessage] fetching products', [
                    'product_ids' => $productIds,
                ]);
                $products = $this->fetchShopProducts($productIds);
                Log::info('[ChatController.pollMessage] products fetched', [
                    'count' => count($products),
                ]);
                $response['products'] = $products;
            } else {
                $response['products'] = [];
            }

            $response['currency'] = [
                'prefix' => Currency::$prefix[session('currency', 'usd')] ?? '$',
                'code' => session('currency', 'usd'),
                'coef' => (float) session('currency_c', 1),
            ];

            $this->appendToHistory('assistant', $response['answer'] ?? '', $response['products']);
        }

        return response()->json($response);
    }

    private function buildErrorResponse(string $errorCode, int $httpStatus): JsonResponse
    {
        $message = match ($errorCode) {
            'connection_refused' => __('text.chat_error_connection'),
            'request_failed' => __('text.chat_error_connection'),
            'unauthorized' => __('text.chat_error_unauthorized'),
            'rate_limited' => __('text.chat_error_rate_limit'),
            'not_found' => __('text.chat_error_message_not_found'),
            'bad_request' => __('text.chat_error_bad_request'),
            'validation_error' => __('text.chat_error_validation'),
            'server_error' => __('text.chat_error_server'),
            default => __('text.chat_error_unknown'),
        };

        Log::warning('[ChatController] error response', [
            'error_code' => $errorCode,
            'http_status' => $httpStatus,
            'message' => $message,
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
        ], $httpStatus >= 400 && $httpStatus < 600 ? $httpStatus : 503);
    }

    private function fetchShopProducts(array $productIds): array
    {
        $products = [];
        $languageId = Language::$languages[app()->getLocale()] ?? Language::$languages['en'];

        $productDescs = ProductDesc::query()
            ->whereIn('language_id', array_unique([$languageId, Language::$languages['en']]))
            ->whereIn('product_id', $productIds)
            ->get(['product_id', 'language_id', 'name', 'url'])
            ->groupBy('product_id')
            ->toArray();

        foreach ($productIds as $apiProductId) {
            $product = Product::where('id', $apiProductId)
                ->where('is_showed', 1)
                ->first();

            if (!$product) {
                Log::warning('[ChatController.fetchShopProducts] product not found', [
                    'product_id' => $apiProductId,
                ]);
                continue;
            }

            $productDescList = $productDescs[$product->id] ?? [];
            $productDesc = null;
            foreach ($productDescList as $desc) {
                if ((int) $desc['language_id'] === (int) $languageId) {
                    $productDesc = $desc;
                    break;
                }
            }
            if (!$productDesc && !empty($productDescList)) {
                $productDesc = $productDescList[0];
            }
            $productName = (!empty($productDesc['name']) ? $productDesc['name'] : null) ?? $apiProductId;
            $productSlug = $productDesc['url'] ?? '';

            $packs = ProductPackaging::where('product_id', $product->id)
                ->where('price', '!=', 0)
                ->where('is_showed', 1)
                ->orderBy('ord')
                ->get();

            $packData = [];
            foreach ($packs as $pack) {
                $packData[] = [
                    'id' => $pack->id,
                    'quantity' => $pack->num,
                    'dosage' => $pack->dosage,
                    'price' => (float) $pack->price,
                    'old_price' => (float) $pack->old_price,
                    'delivery' => $pack->delivery_info ?? '',
                    'add_url' => route('cart.add_pack', ['pack_id' => $pack->id]),
                ];
            }

            if (empty($packData)) {
                Log::debug('[ChatController.fetchShopProducts] no packs for product', [
                    'product_id' => $product->id,
                ]);
                continue;
            }

            $minPrice = min(array_column($packData, 'price'));
            $firstPack = $packData[0];

            $products[] = [
                'id' => $product->id,
                'name' => $productName,
                'slug' => $productSlug,
                'image' => $product->image ? route('home.set_images', $product->image) : '',
                'dosage' => $firstPack['dosage'] ?? '',
                'min_price' => $minPrice,
                'packs' => $packData,
            ];
        }

        return $products;
    }

    private function appendToHistory(string $role, string $content, array $products = []): void
    {
        $history = session('chat_history', []);
        $maxMessages = 50;

        $history[] = [
            'role' => $role,
            'content' => $content,
            'products' => $products,
            'time' => now()->toIso8601String(),
        ];

        if (count($history) > $maxMessages) {
            $history = array_slice($history, -$maxMessages);
        }

        session(['chat_history' => $history]);
        session()->save();

        Log::debug('[ChatController.appendToHistory] saved', [
            'role' => $role,
            'history_count' => count($history),
        ]);
    }

    public function getHistory(): JsonResponse
    {
        $history = session('chat_history', []);

        Log::debug('[ChatController.getHistory]', [
            'count' => count($history),
        ]);

            return response()->json([
                'success' => true,
                'status' => 'processing',
            ]);
        }
}
