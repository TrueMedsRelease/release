<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SessionHelper
{
    const VISIT_TIMEOUT_MIN       = 120;
    const DEFAULT_COOKIE_LIFETIME = 365 * 24 * 60; // 365 days in minutes

    public static function computeSessionId(Request $request): string
    {
        $sessionId = $request->cookie('tm_session_id');

        // Validate existing session ID
        if ($sessionId && self::isValidUuid($sessionId)) {
            session(['tm_session_id' => $sessionId]);
            return $sessionId;
        }

        $newSessionId = Str::uuid()->toString();

        // Store in session for current request
        session(['tm_session_id' => $newSessionId]);

        return $newSessionId;
    }

    public static function getSessionId(Request $request): string
    {
        $sessionId = session('tm_session_id');
        if (empty($sessionId) || !self::isValidUuid($sessionId)) {
            $sessionId = static::computeSessionId($request);
        }

        return $sessionId;
    }

    public static function getVisitId(Request $request): string
    {
        $visitId = session('tm_visit_id');
        if (empty($visitId) || !self::isValidUuid($visitId)) {
            $visitData = static::computeVisitId($request);
            $visitId   = $visitData['visit_id'];
        }

        return $visitId;
    }

    public static function computeVisitId(Request $request): array
    {
        $currentSignature = self::currentAttributionSignature($request);

        $visitData = self::getVisitDataFromCookie($request);

        $existingVisitId   = $visitData['visit_id'] ?? null;
        $existingSignature = $visitData['signature'] ?? null;

        // Validate existing visit ID
        $isValidExistingId = $existingVisitId && self::isValidUuid($existingVisitId);

        // If we have existing visit data and signature matches, reuse it.
        if ($isValidExistingId && $existingSignature === $currentSignature) {
            $visitId = $existingVisitId;
            $isUniq  = false;
        } else {
            // Signature changed or no existing data — generate new visit_id
            $visitId = Str::uuid()->toString();
            $isUniq  = true;
        }

        return [
            'visit_id'  => $visitId,
            'is_uniq'   => $isUniq,
            'signature' => $currentSignature
        ];
    }

    public static function currentAttributionSignature(Request $request): string
    {
        $parts = [
            self::getParamWithCookieFallback($request, 'aff', 'js_stat_aff_id', config('app.aff')),
            self::getParamWithCookieFallback($request, 'click_id', 'js_stat_click_id', ''),
            self::getParamWithCookieFallback($request, 'saff', 'js_stat_saff', ''),
            self::getParamWithCookieFallback($request, 'utm_source', 'js_stat_utm_source', ''),
            self::getParamWithCookieFallback($request, 'utm_medium', 'js_stat_utm_medium', ''),
            self::getParamWithCookieFallback($request, 'utm_campaign', 'js_stat_utm_campaign', ''),
            self::getParamWithCookieFallback($request, 'utm_term', 'js_stat_utm_term', ''),
            self::getParamWithCookieFallback($request, 'utm_content', 'js_stat_utm_content', ''),
        ];

        // Filter out null values and ensure all parts are strings
        $stringParts = array_map(fn($part) => (string)$part, $parts);

        return implode('|', $stringParts);
    }

    public static function getParamWithCookieFallback(
        Request $request,
        string $paramName,
        string $cookieName,
        mixed $defaultValue = null
    ): mixed {
        // 1. Check query string first
        $qsValue = $request->query($paramName);
        if ($qsValue !== null && $qsValue !== '') {
            return self::safeString($qsValue);
        }

        // 2. If not in query string, check cookie
        $cookieValue = $request->cookie($cookieName);
        if ($cookieValue !== null && $cookieValue !== '') {
            return self::safeString($cookieValue);
        }

        // 3. Return default value
        return $defaultValue;
    }

    public static function getInitialReferrer(Request $request): string
    {
        // Try to get from cookie first
        $cookieReferrer = $request->cookie('tm_initial_referrer');
        if ($cookieReferrer && is_string($cookieReferrer)) {
            return $cookieReferrer;
        }

        // If no cookie, use current referrer
        $currentReferrer = $request->header('referer', 'no referrer');

        return is_string($currentReferrer) ? $currentReferrer : 'no referrer';
    }

    private static function getVisitDataFromCookie(Request $request): array
    {
        $visitDataCookie = $request->cookie('tm_visit_data');

        if (!$visitDataCookie || !is_string($visitDataCookie)) {
            return [];
        }

        try {
            $data = json_decode($visitDataCookie, true, 512, JSON_THROW_ON_ERROR);
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            return [];
        }
    }

    private static function safeString(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            return (string)$value;
        }

        return '';
    }

    private static function isValidUuid(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return (bool)preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $value
        );
    }
}