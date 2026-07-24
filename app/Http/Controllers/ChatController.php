<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductDesc;
use App\Models\ProductPackaging;
use App\Models\ProductTypeDesc;
use App\Services\MedicalAssistantService;
use App\Services\ProductServices;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
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
        $msgCount = (int) session('chat_msg_count', 0);
        $needsCaptcha = $msgCount === 0
            || (!app()->environment('production') ? false : $msgCount % 5 === 4);

        if ($needsCaptcha) {
            $captchaCode = $request->input('captcha_code', '');
            if ($captchaCode === '') {
                return response()->json([
                    'success' => false,
                    'captcha_required' => true,
                    'captcha_src' => captcha_src(),
                ]);
            }
            if (!captcha_check($captchaCode)) {
                return response()->json([
                    'success' => false,
                    'captcha_required' => true,
                    'captcha_src' => captcha_src(),
                    'message' => __('text.errors_wrong_captcha_value'),
                ]);
            }
        }

        session(['chat_msg_count' => $msgCount + 1]);
        session()->save();
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

            if (!config('medbot.enabled', true)) {
                Cache::forget($pendingKey);
                Log::info('[ChatController.pollMessage] fallback activated (medbot disabled)', [
                    'query' => $pending['query'] ?? '',
                ]);
                return $this->buildFallbackResponse(
                    $pending['query'] ?? '',
                    $this->performFallbackSearch($pending['query'] ?? '')
                );
            }

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

                if ($e instanceof ConnectionException) {
                    Cache::forget($pendingKey);
                    Log::info('[ChatController.pollMessage] fallback activated (sendQuery ConnectionException)', [
                        'query' => $pending['query'] ?? '',
                    ]);
                    return $this->buildFallbackResponse(
                        $pending['query'] ?? '',
                        $this->performFallbackSearch($pending['query'] ?? '')
                    );
                }

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

                if ($this->isFallbackError($result['error'])) {
                    Cache::forget($pendingKey);
                    Log::info('[ChatController.pollMessage] fallback activated (sendQuery error)', [
                        'query' => $pending['query'] ?? '',
                        'error' => $result['error'],
                    ]);
                    $products = $this->performFallbackSearch($pending['query'] ?? '');
                    $response = $this->buildFallbackResponse($pending['query'] ?? '', $products);
                    $this->appendToHistory('assistant', $response->getData(true)['answer'] ?? '', $products);
                    return $response;
                }

                $pending['resolving'] = false;
                Cache::put($pendingKey, $pending, 120);
                return response()->json([
                    'success' => true,
                    'status' => 'queued',
                ]);
            }

            $realId = $result['data']['id'] ?? null;
            if (!$realId) {
                Log::error('[ChatController.pollMessage] no id in response', [
                    'local_id' => $messageId,
                ]);
                $pending['resolving'] = false;
                Cache::put($pendingKey, $pending, 120);
                return response()->json([
                    'success' => true,
                    'status' => 'queued',
                ]);
            }

            Cache::forget($pendingKey);
            Cache::put("chat_real_id:{$messageId}", $realId, 300);
            Cache::put("chat_query:{$messageId}", $pending['query'] ?? '', 300);

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

        if (!Str::isUuid($messageId)) {
            Log::warning('[ChatController.pollMessage] invalid message_id format', [
                'msg_id' => $messageId,
            ]);
            return response()->json([
                'success' => false,
                'message' => __('text.chat_error_message_not_found'),
            ]);
        }

        $localId = $messageId;
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

        try {
            $result = $this->medicalAssistant->pollStatus($messageId);

            if (!$result['ok']) {
                if ($this->isFallbackError($result['error'])) {
                    $query = Cache::get("chat_query:{$localId}", '');
                    Log::info('[ChatController.pollMessage] fallback activated (pollStatus error)', [
                        'msg_id' => $messageId,
                        'query' => $query,
                        'error' => $result['error'],
                    ]);
                    $products = $this->performFallbackSearch($query);
                    $response = $this->buildFallbackResponse($query, $products);
                    $this->appendToHistory('assistant', $response->getData(true)['answer'] ?? '', $products);
                    return $response;
                }

                return $this->buildErrorResponse($result['error'], $result['http_status']);
            }

            $data = $result['data'];
            $status = $data['status'] ?? 'unknown';

            $knownStatuses = ['queued', 'processing', 'done', 'error', 'expired', 'abandoned', 'unknown'];
            if (!in_array($status, $knownStatuses, true)) {
                Log::warning('[ChatController.pollMessage] unknown status from API', [
                    'msg_id' => $messageId,
                    'status' => $status,
                ]);
                $status = 'processing';
            }

            if (in_array($status, ['expired', 'abandoned'], true)) {
                Log::warning('[ChatController.pollMessage] terminal error status', [
                    'msg_id' => $messageId,
                    'status' => $status,
                ]);
                $query = Cache::get("chat_query:{$localId}", '');
                if ($query !== '') {
                    $products = $this->performFallbackSearch($query);
                    $response = $this->buildFallbackResponse($query, $products);
                    $this->appendToHistory('assistant', $response->getData(true)['answer'] ?? '', $products);
                    return $response;
                }
                return $this->buildErrorResponse('server_error', 500);
            }

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
        } catch (\Exception $e) {
            Log::error('[ChatController.pollMessage] exception in polling path', [
                'msg_id' => $messageId,
                'local_id' => $localId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $query = Cache::get("chat_query:{$localId}", '');
            if ($query !== '') {
                Log::info('[ChatController.pollMessage] fallback activated (exception in polling)', [
                    'query' => $query,
                ]);
                $products = $this->performFallbackSearch($query);
                $response = $this->buildFallbackResponse($query, $products);
                $this->appendToHistory('assistant', $response->getData(true)['answer'] ?? '', $products);
                return $response;
            }

            return $this->buildErrorResponse('server_error', 500);
        }
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

    private function isFallbackError(string $errorCode): bool
    {
        return in_array($errorCode, ['connection_refused', 'request_failed', 'server_error'], true);
    }

    private function performFallbackSearch(string $query): array
    {
        if ($query === '') {
            Log::warning('[ChatController.performFallbackSearch] empty query');
            return [];
        }

        try {
            $searchResults = ProductServices::SearchProduct($query, false, 'design_17');
            $productIds = array_column($searchResults, 'id');

            Log::info('[ChatController.performFallbackSearch] search completed', [
                'query' => $query,
                'total_results' => count($searchResults),
                'product_ids' => $productIds,
            ]);

            if (empty($productIds)) {
                Log::warning('[ChatController.performFallbackSearch] no products found', [
                    'query' => $query,
                ]);
                return [];
            }

            $productIds = array_slice($productIds, 0, 30);
            $products = $this->fetchShopProducts($productIds);
            return $products;
        } catch (\Exception $e) {
            Log::error('[ChatController.performFallbackSearch] exception', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    private function buildFallbackResponse(string $query, array $products): JsonResponse
    {
        if (empty($products)) {
            $answer = __('text.search_result_nothing_found1') . ' «' . $query . '» ' . __('text.search_result_nothing_found2') . '. ' . __('text.search_result_nothing_found3');
        } else {
            $answer = __('text.search_result_title_page') . ' «' . $query . '».';
        }

        Log::info('[ChatController.buildFallbackResponse]', [
            'query' => $query,
            'products_count' => count($products),
        ]);

        return response()->json([
            'success' => true,
            'status' => 'done',
            'answer' => $answer,
            'products' => $products,
            'currency' => [
                'prefix' => Currency::$prefix[session('currency', 'usd')] ?? '$',
                'code' => session('currency', 'usd'),
                'coef' => (float) session('currency_c', 1),
            ],
            'steps' => [],
        ]);
    }

    private function fetchShopProducts(array $productIds): array
    {
        $products = [];
        $languageId = Language::$languages[app()->getLocale()] ?? Language::$languages['en'];

        $productDescs = ProductDesc::query()
            ->whereIn('language_id', array_unique([$languageId, Language::$languages['en']]))
            ->whereIn('product_id', $productIds)
            ->get(['product_id', 'language_id', 'name', 'url', 'desc'])
            ->groupBy('product_id')
            ->toArray();

        $typeNames = ProductTypeDesc::query()
            ->where('language_id', '=', $languageId)
            ->pluck('name', 'type_id')
            ->toArray();
        if (empty($typeNames)) {
            $typeNames = ProductTypeDesc::query()
                ->where('language_id', '=', 1)
                ->pluck('name', 'type_id')
                ->toArray();
        }

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
            $productDescText = $productDesc['desc'] ?? '';
            $productFullDesc = '';

            if ($product->image) {
                $locale = app()->getLocale();
                $descPath = public_path("language_codes/{$locale}/{$product->image}.html");
                $descPathEn = public_path("language_codes/en/{$product->image}.html");

                if (File::exists($descPath)) {
                    $raw = File::get($descPath);
                } elseif (File::exists($descPathEn)) {
                    $raw = File::get($descPathEn);
                } else {
                    $raw = '';
                }

                if ($raw !== '') {
                    $replacements = [
                        '#TOP_TAG#' => '<div class="full_text" style="margin-top: 16px;">',
                        '#TITLE_OPEN_TAG#' => '<h3>',
                        '#TITLE_CLOSE_TAG#' => '</h3>',
                        '#BLOCK_OPEN_TAG#' => '<p>',
                        '#BLOCK_CLOSE_TAG#' => '</p>',
                        '#LIST_OPEN_TAG#' => '<ul>',
                        '#LIST_ELEMENT_OPEN_TAG#' => '<li>',
                        '#LIST_ELEMENT_CLOSE_TAG#' => '</li>',
                        '#LIST_CLOSE_TAG#' => '</ul>',
                        '#BOTTOM_TAG#' => '</div>',
                        '#NEXT_LINE_TAG#' => '<br />',
                    ];
                    $productFullDesc = str_replace(
                        array_keys($replacements),
                        array_values($replacements),
                        $raw
                    );
                }
            }

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
                    'type_id' => $pack->type_id,
                    'unit' => $typeNames[$pack->type_id] ?? '',
                    'price_per_pill' => $pack->num > 0 ? round($pack->price / $pack->num, 2) : 0,
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
            $productTypeId = $firstPack['type_id'] ?? 1;
            $productTypeName = $typeNames[$productTypeId] ?? '';

            $products[] = [
                'id' => $product->id,
                'name' => $productName,
                'slug' => $productSlug,
                'image' => $product->image ? route('home.set_images', $product->image) : '',
                'dosage' => $firstPack['dosage'] ?? '',
                'desc' => $productDescText,
                'full_desc' => $productFullDesc,
                'min_price' => $minPrice,
                'packs' => $packData,
                'product_type' => $productTypeName,
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
        $history = array_slice($history, -20);

        Log::debug('[ChatController.getHistory]', [
            'count' => count($history),
        ]);

        return response()->json([
            'success' => true,
            'messages' => array_values($history),
            'currency' => [
                'prefix' => Currency::$prefix[session('currency', 'usd')] ?? '$',
                'code' => session('currency', 'usd'),
                'coef' => (float) session('currency_c', 1),
            ],
        ]);
    }
}
