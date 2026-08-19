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
    private const GIFT_PRODUCT_ID = 616;
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
        // $msgCount = (int) session('chat_msg_count', 0);
        // $needsCaptcha = $msgCount === 0
        //     || (!app()->environment('production') ? false : $msgCount % 5 === 4);

        // if ($needsCaptcha) {
        //     $captchaCode = $request->input('captcha_code', '');
        //     if ($captchaCode === '') {
        //         return response()->json([
        //             'success' => false,
        //             'captcha_required' => true,
        //             'captcha_src' => captcha_src(),
        //         ]);
        //     }
        //     if (!captcha_check($captchaCode)) {
        //         return response()->json([
        //             'success' => false,
        //             'captcha_required' => true,
        //             'captcha_src' => captcha_src(),
        //             'message' => __('text.errors_wrong_captcha_value'),
        //         ]);
        //     }
        // }

        // session(['chat_msg_count' => $msgCount + 1]);
        // session()->save();
        $language = $this->resolveCurrentLocale();
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

            $medbotEnabled = (bool) config('medbot.enabled', true);
            $hasApiKey = $this->medicalAssistant->hasApiKey();

            if (!$medbotEnabled || !$hasApiKey) {
                Cache::forget($pendingKey);

                $fallbackReason = !$medbotEnabled
                    ? 'medbot disabled'
                    : 'APP_BOT_KEY is empty';

                Log::info('[ChatController.pollMessage] fallback activated', [
                    'query' => $pending['query'] ?? '',
                    'reason' => $fallbackReason,
                ]);

                return $this->fallbackToSearch($pending['query'] ?? '', 'service_unavailable');
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

                Cache::forget($pendingKey);

                Log::info('[ChatController.pollMessage] fallback activated (exception)', [
                    'query' => $pending['query'] ?? '',
                    'error' => $e->getMessage(),
                ]);

                return $this->fallbackToSearch($pending['query'] ?? '', 'service_error');
            }

            if (!$result['ok']) {
                Log::error('[ChatController.pollMessage] sendQuery failed', [
                    'local_id' => $messageId,
                    'error' => $result['error'],
                ]);

                Cache::forget($pendingKey);

                Log::info('[ChatController.pollMessage] fallback activated', [
                    'query' => $pending['query'] ?? '',
                    'error' => $result['error'],
                ]);

                return $this->fallbackToSearch($pending['query'] ?? '', 'service_error');
            }

            $realId = $result['data']['id'] ?? null;
            if (!$realId) {
                Log::error('[ChatController.pollMessage] no id in response', [
                    'local_id' => $messageId,
                ]);

                Cache::forget($pendingKey);

                Log::info('[ChatController.pollMessage] fallback activated (missing id)', [
                    'query' => $pending['query'] ?? '',
                ]);

                return $this->fallbackToSearch($pending['query'] ?? '', 'service_error');
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
                    return $this->fallbackToSearch($query, 'service_error');
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
                    return $this->fallbackToSearch($query, 'service_error');
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

            if ($status === 'error') {
                $query = Cache::get("chat_query:{$localId}", '');

                Log::info('[ChatController.pollMessage] medbot returned error status, fallback to shop search', [
                    'msg_id' => $messageId,
                    'local_id' => $localId,
                    'query' => $query,
                ]);

                return $this->fallbackToSearch($query, 'medbot_error');
            }

            if ($status === 'done' && !empty($data['result'])) {
                $apiResult = $data['result'] ?? [];
                $productIds = $apiResult['product_ids'] ?? [];
                $isShopQuestion = (bool) ($apiResult['is_shop_question'] ?? $data['is_shop_question'] ?? false);

                if (empty($productIds) && !$isShopQuestion) {
                    $query = Cache::get("chat_query:{$localId}", '');

                    Log::info('[ChatController.pollMessage] medbot returned empty product list, fallback to shop search', [
                        'msg_id' => $messageId,
                        'local_id' => $localId,
                        'query' => $query,
                    ]);

                    return $this->fallbackToSearch($query, 'empty_products');
                }

                if (!empty($productIds)) {
                    Log::debug('[ChatController.pollMessage] fetching products', [
                        'product_ids' => $productIds,
                    ]);

                    $products = $this->fetchShopProducts($productIds);

                    Log::info('[ChatController.pollMessage] products fetched', [
                        'count' => count($products),
                    ]);

                    if (empty($products) && !$isShopQuestion) {
                        $query = Cache::get("chat_query:{$localId}", '');

                        Log::info('[ChatController.pollMessage] medbot products not found in shop, fallback to shop search', [
                            'msg_id' => $messageId,
                            'local_id' => $localId,
                            'query' => $query,
                        ]);

                        return $this->fallbackToSearch($query, 'empty_products');
                    }
                }

                $response['answer'] = $apiResult['answer'] ?? '';
                $response['language'] = $apiResult['language'] ?? 'en';
                $response['product_ids'] = $productIds;
                $response['products'] = $products ?? [];
                $response['is_shop_question'] = $isShopQuestion;
                $response['currency'] = [
                    'prefix' => Currency::$prefix[session('currency', 'usd')] ?? '$',
                    'code' => session('currency', 'usd'),
                    'coef' => (float) session('currency_c', 1),
                ];

                $this->appendToHistory(
                    'assistant',
                    $response['answer'] ?? '',
                    $response['products']
                );
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
                return $this->fallbackToSearch($query, 'service_error');
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
        return in_array($errorCode, [
            'missing_api_key',
            'access_denied',
            'connection_refused',
            'request_failed',
            'server_error',
        ], true);
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

    private function buildFallbackResponse(string $query, array $products, string $reason = "service_error"): JsonResponse
    {
        $catalogUrl = $this->getCatalogUrl();
        $catalogLabel = $this->getCatalogLabel();

        $showCatalogLink = false;

        if (empty($products)) {
            $products = $this->getBestsellerProducts(8);
            $showCatalogLink = true;

            $key = 'text.chat_fallback_not_found';
            $answer = __($key, ['query' => $query]);

            if ($answer === $key) {
                $answer = 'We couldn\'t find anything for your query "' . $query . '". You can browse our catalog and see our bestsellers below.';
            }
        } else {
            $answer = __('text.search_result_title_page') . ' «' . $query . '».';
        }

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
            'fallback' => true,
            'fallback_reason' => $reason,
            'show_catalog_link' => $showCatalogLink,
            'catalog_url' => $catalogUrl,
            'catalog_label' => $catalogLabel,
        ]);
    }

    private function getCatalogUrl(): string
    {
        return route('catalog.index');
    }

    private function getCatalogLabel(): string
    {
        $key = 'text.chat_catalog_link';
        $value = __($key);

        return $value === $key ? 'View our catalog' : $value;
    }

    private function getBestsellerProducts(int $limit = 8): array
    {
        try {
            $bestsellers = ProductServices::GetBestsellers('design_17');

            if (is_object($bestsellers) && method_exists($bestsellers, 'toArray')) {
                $bestsellers = $bestsellers->toArray();
            }

            $ids = [];

            foreach ((array) $bestsellers as $item) {
                if (is_array($item)) {
                    if (!empty($item['id'])) {
                        $ids[] = (int) $item['id'];
                    } elseif (!empty($item['product_id'])) {
                        $ids[] = (int) $item['product_id'];
                    }
                } elseif (is_object($item)) {
                    if (!empty($item->id)) {
                        $ids[] = (int) $item->id;
                    } elseif (!empty($item->product_id)) {
                        $ids[] = (int) $item->product_id;
                    }
                }
            }

            if (empty($ids)) {
                $ids = $this->extractProductIds($bestsellers);
            }

            $ids = array_values(array_unique(array_filter($ids)));
            $ids = array_slice($ids, 0, $limit);

            if (empty($ids)) {
                return [];
            }

            return $this->fetchShopProducts($ids);
        } catch (\Throwable $e) {
            Log::error('[ChatController.getBestsellerProducts] exception', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function resolveCurrentLocale(): string
    {
        $locale = strtolower(trim((string) session(
            'locale',
            app()->getLocale() ?: config('app.language', 'en')
        )));

        if (!isset(Language::$languages[$locale])) {
            $locale = strtolower(trim((string) config('app.language', 'en')));
        }

        if (!isset(Language::$languages[$locale])) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        return $locale;
    }

    private function fetchShopProducts(array $productIds): array
    {
        $products = [];
        $locale = $this->resolveCurrentLocale();
        $englishLanguageId = Language::$languages['en'];
        $languageId = Language::$languages[$locale] ?? $englishLanguageId;

        $productDescs = ProductDesc::query()
            ->whereIn('language_id', array_unique([$languageId, $englishLanguageId]))
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
            if ((int) $apiProductId === self::GIFT_PRODUCT_ID) {
                continue;
            }

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
            $englishProductDesc = null;

            foreach ($productDescList as $desc) {
                $descLanguageId = (int) ($desc['language_id'] ?? 0);

                if ($descLanguageId === (int) $languageId) {
                    $productDesc = $desc;
                    break;
                }

                if ($descLanguageId === (int) $englishLanguageId) {
                    $englishProductDesc = $desc;
                }
            }

            if (!$productDesc) {
                $productDesc = $englishProductDesc;
            }
            $productName = (!empty($productDesc['name']) ? $productDesc['name'] : null) ?? $apiProductId;
            $productSlug = $productDesc['url'] ?? '';
            $productDescText = $productDesc['desc'] ?? '';
            $productFullDesc = '';

            if ($product->image) {
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

            $productActive = explode(',', ucwords(trim(str_replace("\r\n", '', trim($product->aktiv)))));

            foreach ($productActive as $key => $value) {
                $activeUrl = str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))));

                $productActive[$key] = [
                    'type' => 'active',
                    'name' => trim($value),
                    'slug' => $activeUrl,
                    'url'  => route('home.active', $activeUrl, false),
                ];
            }

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
                'active' => $productActive,
            ];
        }

        return $products;
    }

    private function appendToHistory(string $role, string $content, array $products = [], array $meta = []): void
    {
        $history = session('chat_history', []);
        $maxMessages = 50;

        $message = [
            'role' => $role,
            'content' => $content,
            'products' => $products,
            'time' => now()->toIso8601String(),
        ];

        if (!empty($meta)) {
            $message = array_merge($message, $meta);
        }

        $history[] = $message;

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

        $productIds = [];

        foreach ($history as $message) {
            foreach (($message['products'] ?? []) as $product) {
                $productId = (int) ($product['id'] ?? 0);

                if ($productId > 0) {
                    $productIds[] = $productId;
                }
            }
        }

        $localizedProductsById = [];

        if (!empty($productIds)) {
            $localizedProducts = $this->fetchShopProducts(
                array_values(array_unique($productIds))
            );

            foreach ($localizedProducts as $product) {
                $localizedProductsById[(int) $product['id']] = $product;
            }

            foreach ($history as &$message) {
                if (empty($message['products']) || !is_array($message['products'])) {
                    continue;
                }

                $message['products'] = array_values(array_filter(array_map(
                    static function (array $product) use ($localizedProductsById): array {
                        $productId = (int) ($product['id'] ?? 0);
                        return $localizedProductsById[$productId] ?? $product;
                    },
                    $message['products']
                ), static function (array $product): bool {
                    return (int) ($product['id'] ?? 0) !== self::GIFT_PRODUCT_ID;
                }));
            }
            unset($message);

            session(['chat_history' => $history]);
            session()->save();
        }

        Log::debug('[ChatController.getHistory]', [
            'count' => count($history),
            'locale' => $this->resolveCurrentLocale(),
            'localized_product_ids' => array_values(array_unique($productIds)),
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

    private function fallbackToSearch(string $query, string $reason = 'service_error'): JsonResponse
    {
        $products = $this->performFallbackSearch($query);

        $response = $this->buildFallbackResponse($query, $products);

        $data = $response->getData(true);

        $this->appendToHistory(
            'assistant',
            $data['answer'] ?? '',
            $data['products'] ?? [],
            [
                'fallback' => $data['fallback'] ?? true,
                'fallback_reason' => $data['fallback_reason'] ?? $reason,
                'show_catalog_link' => $data['show_catalog_link'] ?? false,
                'catalog_url' => $data['catalog_url'] ?? null,
                'catalog_label' => $data['catalog_label'] ?? null,
            ]
        );

        return $response;
    }

    public function browseCollection(Request $request, string $type, string $slug): JsonResponse
    {
        $allowedTypes = ['active', 'category', 'disease', 'first_letter', 'search'];

        if (!in_array($type, $allowedTypes, true)) {
            return response()->json([
                'success' => false,
                'message' => __('text.chat_error_unknown'),
            ], 422);
        }

        $slug = $this->normalizeBrowseSlug($slug);

        if ($slug === '') {
            return response()->json([
                'success' => false,
                'message' => __('text.chat_error_bad_request'),
            ], 422);
        }

        $language = $this->resolveCurrentLocale();

        Log::info('[ChatController.browseCollection] new browse query', [
            'type' => $type,
            'slug' => $slug,
            'language' => $language,
            'ip' => $request->ip(),
        ]);

        $design = 'design_17';
        $products = [];

        try {
            $rawProducts = match ($type) {
                'active' => ProductServices::GetProductByActive($slug, $design),
                'category' => ProductServices::GetCategoriesWithProducts($design, $slug),
                'disease' => ProductServices::GetProductByDisease($slug, $design),
                'first_letter' => ProductServices::GetProductByFirstLetter($slug, $design),
                'search' => ProductServices::SearchProduct($slug, false, $design),
                default => [],
            };

            $productIds = $this->extractProductIds($rawProducts);
            $productIds = array_slice($productIds, 0, 30);

            if (!empty($productIds)) {
                $products = $this->fetchShopProducts($productIds);
            }
        } catch (\Throwable $e) {
            Log::error('[ChatController.browseCollection] exception', [
                'type' => $type,
                'slug' => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $products = [];
        }

        $label = $this->makeBrowseLabel($slug);

        $userMessage = $this->buildBrowseUserMessage($type, $label);

        if (empty($products)) {
            $answer =
                __('text.search_result_nothing_found1')
                . ' «' . $label . '» '
                . __('text.search_result_nothing_found2') . '. '
                . __('text.search_result_nothing_found3');
        } else {
            $answer = __('text.search_result_title_page') . ' «' . $label . '».';
        }

        $this->appendToHistory('user', $userMessage);
        $this->appendToHistory('assistant', $answer, $products);

        Log::info('[ChatController.browseCollection] response prepared', [
            'type' => $type,
            'slug' => $slug,
            'products_count' => count($products),
        ]);

        return response()->json([
            'success' => true,
            'status' => 'done',
            'answer' => $answer,
            'products' => $products,
            'steps' => [],
            'currency' => [
                'prefix' => Currency::$prefix[session('currency', 'usd')] ?? '$',
                'code' => session('currency', 'usd'),
                'coef' => (float) session('currency_c', 1),
            ],
        ]);
    }

    private function normalizeBrowseSlug(string $slug): string
    {
        $slug = rawurldecode($slug);

        $locale = $this->resolveCurrentLocale();

        if (in_array($locale, ['hant', 'hans', 'gr', 'arb', 'ja'], true)) {
            $text1 = __('text.text_aff_domain_1', [], 'en');
            $text2 = __('text.text_aff_domain_2', [], 'en');
        } else {
            $text1 = __('text.text_aff_domain_1');
            $text2 = __('text.text_aff_domain_2');
        }

        $slug = str_replace(
            [
                $text1 . '_',
                '_' . $text2,
            ],
            '',
            $slug
        );

        $slug = trim($slug);
        $slug = trim($slug, '/');

        return $slug;
    }

    private function makeBrowseLabel(string $slug): string
    {
        $label = str_replace(['-', '_'], ' ', $slug);
        $label = trim($label);

        if ($label === '') {
            return $slug;
        }

        if (function_exists('mb_convert_case')) {
            return mb_convert_case($label, MB_CASE_TITLE, 'UTF-8');
        }

        return ucwords($label);
    }

    private function buildBrowseUserMessage(string $type, string $label): string
    {
        $prefix = match ($type) {
            'active' => $this->translateOrDefault('text.chat_browse_active', 'Show products with active ingredient'),
            'category' => $this->translateOrDefault('text.chat_browse_category', 'Show products from category'),
            'disease' => $this->translateOrDefault('text.chat_browse_disease', 'Show products for'),
            'first_letter' => $this->translateOrDefault('text.chat_browse_first_letter', 'Products starting with'),
            'search' => $this->translateOrDefault('text.chat_browse_search', 'Search'),
            default => $this->translateOrDefault('text.chat_browse_default', 'Show'),
        };

        return trim($prefix . ' ' . $label);
    }

    private function translateOrDefault(string $key, string $default): string
    {
        $value = __($key);

        return $value === $key ? $default : $value;
    }

    private function extractProductIds($data): array
    {
        $ids = [];

        $this->collectProductIds($data, $ids);

        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, function ($id) {
            return $id > 0;
        });

        $ids = array_unique($ids);

        return array_values($ids);
    }

    private function collectProductIds($data, array &$ids): void
    {
        if (is_object($data)) {
            if (method_exists($data, 'toArray')) {
                $data = $data->toArray();
            } else {
                $data = get_object_vars($data);
            }
        }

        if (!is_array($data)) {
            return;
        }

        if (isset($data['product_id']) && is_numeric($data['product_id'])) {
            $ids[] = (int) $data['product_id'];
        }

        if (isset($data['id']) && is_numeric($data['id'])) {
            $looksLikeProduct =
                isset($data['url'])
                || isset($data['name'])
                || isset($data['packs'])
                || isset($data['product_id']);

            if ($looksLikeProduct) {
                $ids[] = (int) $data['id'];
            }
        }

        if (isset($data['product_ids']) && is_array($data['product_ids'])) {
            foreach ($data['product_ids'] as $id) {
                if (is_numeric($id)) {
                    $ids[] = (int) $id;
                }
            }
        }

        foreach ($data as $value) {
            if (is_array($value) || is_object($value)) {
                $this->collectProductIds($value, $ids);
            }
        }
    }
}