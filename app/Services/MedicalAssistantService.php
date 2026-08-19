<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MedicalAssistantService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) config('medbot.base_url', 'http://pills-22.com'),
            '/'
        );

        Log::debug('[MedicalAssistantService.__construct] initialized', [
            'base_url' => $this->baseUrl,
            'has_api_key' => $this->hasApiKey(),
        ]);
    }

    /**
     * Проверяет, задан ли ключ Medical Assistant в APP_BOT_KEY.
     */
    public function hasApiKey(): bool
    {
        return $this->apiKey() !== '';
    }

    private function apiKey(): string
    {
        return trim((string) config('medbot.api_key', ''));
    }

    /**
     * Формат X-DOMAIN:
     * example.com?aff_id=123
     */
    private function domainHeader(): string
    {
        $host = '';

        try {
            $host = (string) request()->getHost();
        } catch (\Throwable $e) {
            // Вне HTTP-запроса используем APP_URL.
        }

        if ($host === '') {
            $host = (string) parse_url(
                (string) config('app.url', ''),
                PHP_URL_HOST
            );
        }

        $host = $host !== '' ? $host : 'unknown-domain';
        $affId = (string) session('aff', config('app.aff', 0));

        // return $host . '?aff_id=' . rawurlencode($affId);
        return $host;
    }

    private function http(): PendingRequest
    {
        return Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => $this->apiKey(),
                'X-DOMAIN' => $this->domainHeader(),
            ])
            ->timeout(config('medbot.timeout', 15))
            ->connectTimeout(config('medbot.connect_timeout', 3));
    }

    public function sendQuery(string $query, string $language = 'en', array $filters = []): array
    {
        if (!$this->hasApiKey()) {
            Log::info('[MedicalAssistantService.sendQuery] skipped: APP_BOT_KEY is empty');
            return $this->error('missing_api_key', 0);
        }

        Log::debug('[MedicalAssistantService.sendQuery] request', [
            'query' => $query,
            'language' => $language,
            'x_domain' => $this->domainHeader(),
        ]);

        $payload = [
            'query' => $query,
            'max_results' => $filters['max_results'] ?? config('medbot.max_results', 5),
        ];

        if (!empty($language)) {
            $payload['filters']['language'] = $language;
        }

        try {
            $response = $this->http()
                ->post("{$this->baseUrl}/medbot/v1/ask/async", $payload);

            $statusCode = $response->status();
            $body = $response->body();

            Log::debug('[MedicalAssistantService.sendQuery] http response', [
                'status' => $statusCode,
            ]);

            if ($this->isAccessDeniedResponse($statusCode, $body)) {
                return $this->logAndReturn(
                    'access_denied',
                    $statusCode,
                    'sendQuery',
                    [],
                    $body
                );
            }

            if ($response->successful()) {
                $data = $response->json();
                Log::info('[MedicalAssistantService.sendQuery] success', [
                    'msg_id' => $data['id'] ?? 'unknown',
                    'status' => $data['status'] ?? 'unknown',
                ]);
                return $this->success($data);
            }

            return $this->resolveError($statusCode, $body, 'sendQuery');

        } catch (ConnectionException $e) {
            Log::error('[MedicalAssistantService.sendQuery] connection error', [
                'error' => $e->getMessage(),
            ]);
            return $this->error('connection_refused', 0);

        } catch (RequestException $e) {
            Log::error('[MedicalAssistantService.sendQuery] request error', [
                'error' => $e->getMessage(),
            ]);
            return $this->error('request_failed', 0);

        } catch (\JsonException $e) {
            Log::error('[MedicalAssistantService.sendQuery] invalid JSON response', [
                'error' => $e->getMessage(),
            ]);
            return $this->error('server_error', 502);
        }
    }

    public function pollStatus(string $messageId): array
    {
        if (!$this->hasApiKey()) {
            Log::info('[MedicalAssistantService.pollStatus] skipped: APP_BOT_KEY is empty', [
                'msg_id' => $messageId,
            ]);
            return $this->error('missing_api_key', 0);
        }

        Log::debug('[MedicalAssistantService.pollStatus] polling', [
            'msg_id' => $messageId,
            'x_domain' => $this->domainHeader(),
        ]);

        try {
            $response = $this->http()
                ->get("{$this->baseUrl}/medbot/v1/ask/async/{$messageId}");

            $statusCode = $response->status();
            $body = $response->body();

            Log::debug('[MedicalAssistantService.pollStatus] http response', [
                'msg_id' => $messageId,
                'status' => $statusCode,
            ]);

            if ($this->isAccessDeniedResponse($statusCode, $body)) {
                return $this->logAndReturn(
                    'access_denied',
                    $statusCode,
                    'pollStatus',
                    ['msg_id' => $messageId],
                    $body
                );
            }

            if ($response->successful()) {
                $data = $response->json();
                Log::info('[MedicalAssistantService.pollStatus] result', [
                    'msg_id' => $messageId,
                    'status' => $data['status'] ?? 'unknown',
                ]);
                return $this->success($data);
            }

            return $this->resolveError($statusCode, $body, 'pollStatus', $messageId);

        } catch (ConnectionException $e) {
            Log::error('[MedicalAssistantService.pollStatus] connection error', [
                'msg_id' => $messageId,
                'error' => $e->getMessage(),
            ]);
            return $this->error('connection_refused', 0);

        } catch (RequestException $e) {
            Log::error('[MedicalAssistantService.pollStatus] request error', [
                'msg_id' => $messageId,
                'error' => $e->getMessage(),
            ]);
            return $this->error('request_failed', 0);

        } catch (\JsonException $e) {
            Log::error('[MedicalAssistantService.pollStatus] invalid JSON response', [
                'msg_id' => $messageId,
                'error' => $e->getMessage(),
            ]);
            return $this->error('server_error', 502);
        }
    }

    public function getProductDetails(string $productId): array
    {
        if (!$this->hasApiKey()) {
            Log::info('[MedicalAssistantService.getProductDetails] skipped: APP_BOT_KEY is empty', [
                'product_id' => $productId,
            ]);
            return $this->error('missing_api_key', 0);
        }

        Log::debug('[MedicalAssistantService.getProductDetails] request', [
            'product_id' => $productId,
            'x_domain' => $this->domainHeader(),
        ]);

        try {
            $response = $this->http()
                ->get("{$this->baseUrl}/medbot/v1/products/{$productId}");

            $statusCode = $response->status();
            $body = $response->body();

            if ($this->isAccessDeniedResponse($statusCode, $body)) {
                return $this->logAndReturn(
                    'access_denied',
                    $statusCode,
                    'getProductDetails',
                    ['product_id' => $productId],
                    $body
                );
            }

            if ($response->successful()) {
                $data = $response->json();
                Log::info('[MedicalAssistantService.getProductDetails] success', [
                    'product_id' => $productId,
                    'name' => $data['name'] ?? 'unknown',
                ]);
                return $this->success($data);
            }

            return $this->resolveError($statusCode, $body, 'getProductDetails', $productId);

        } catch (ConnectionException $e) {
            Log::error('[MedicalAssistantService.getProductDetails] connection error', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);
            return $this->error('connection_refused', 0);

        } catch (RequestException $e) {
            Log::error('[MedicalAssistantService.getProductDetails] request error', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);
            return $this->error('request_failed', 0);

        } catch (\JsonException $e) {
            Log::error('[MedicalAssistantService.getProductDetails] invalid JSON response', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);
            return $this->error('server_error', 502);
        }
    }

    /**
     * API может вернуть Access denied как HTTP 403/401 или даже как текст
     * внутри успешного HTTP-ответа. Во всех этих случаях включается fallback.
     */
    private function isAccessDeniedResponse(int $statusCode, string $body): bool
    {
        if ($statusCode === 403) {
            return true;
        }

        return stripos($body, 'access denied') !== false;
    }

    private function resolveError(int $statusCode, string $body, string $method, string $contextId = ''): array
    {
        $context = $contextId ? ['msg_id' => $contextId] : [];

        return match ($statusCode) {
            400 => $this->logAndReturn('bad_request', $statusCode, $method, $context, $body),
            401 => $this->logAndReturn('unauthorized', $statusCode, $method, $context, $body),
            403 => $this->logAndReturn('access_denied', $statusCode, $method, $context, $body),
            404 => $this->logAndReturn('not_found', $statusCode, $method, $context, $body),
            422 => $this->logAndReturn('validation_error', $statusCode, $method, $context, $body),
            429 => $this->logAndReturn('rate_limited', $statusCode, $method, $context, $body),
            500, 502, 503, 504 => $this->logAndReturn('server_error', $statusCode, $method, $context, $body),
            default => $this->logAndReturn('http_error', $statusCode, $method, $context, $body),
        };
    }

    private function logAndReturn(string $errorCode, int $statusCode, string $method, array $context, string $body): array
    {
        Log::warning("[MedicalAssistantService.{$method}] {$errorCode}", array_merge($context, [
            'http_status' => $statusCode,
            'body' => mb_substr($body, 0, 500),
        ]));
        return $this->error($errorCode, $statusCode);
    }

    private function success(array $data): array
    {
        return [
            'ok' => true,
            'data' => $data,
        ];
    }

    private function error(string $code, int $httpStatus): array
    {
        return [
            'ok' => false,
            'error' => $code,
            'http_status' => $httpStatus,
        ];
    }
}