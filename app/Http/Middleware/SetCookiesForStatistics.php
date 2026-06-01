<?php

namespace App\Http\Middleware;

use App\Helpers\DesignHelper;
use App\Helpers\SessionHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetCookiesForStatistics
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->isCookieNeeded($request)) {
            return $next($request);
        }

        // Compute session and visit data
        $sessionId       = SessionHelper::computeSessionId($request);
        $visitData       = SessionHelper::computeVisitId($request);
        $initialReferrer = SessionHelper::getInitialReferrer($request);

        $cookieList = [
            'js_stat_aff_id'      => $this->getAffiliateId($request),
            'js_stat_design_id'   => $this->getDesignId($request),

            // New cookies for session tracking
            'tm_session_id'       => $sessionId,
            'tm_initial_referrer' => $initialReferrer,
            'tm_visit_data'       => json_encode([
                'visit_id'  => $visitData['visit_id'],
                'signature' => $visitData['signature'],
                'is_uniq'   => $visitData['is_uniq']
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];

        $response = $next($request);

        foreach ($cookieList as $name => $value) {
            if (!$request->hasCookie($name) || $request->cookie($name) != $value) {
                $minutes = $this->getCookieLifetime($name);

                $cookie = cookie(
                    name: $name,
                    value: $value,
                    minutes: $minutes,
                    path: '/',
                    secure: config('session.secure'),
                    httpOnly: false, // false = JavaScript can read cookie
                    sameSite: config('session.same_site', 'lax')
                );

                $response->headers->setCookie($cookie);
            }
        }

        // Store in session for current request usage
        session([
            'tm_session_id'    => $sessionId,
            'tm_visit_id'      => $visitData['visit_id'],
            'tm_visit_is_uniq' => $visitData['is_uniq'],
        ]);

        return $response;
    }

    private function getAffiliateId(Request $request)
    {
        $aff = $request->query('aff');
        if ($aff !== null && $aff !== '') {
            return $aff;
        }

        $aff = $request->cookie('AFF_ID');
        if ($aff !== null && $aff !== '') {
            return $aff;
        }

        $aff = $request->cookie('js_stat_aff_id');
        if ($aff !== null && $aff !== '') {
            return $aff;
        }

        return config('app.aff');
    }

    private function getDesignId(Request $request): string
    {
        $isPwa = $this->isPwaRequest($request);

        $designId = $request->query('design');

        if ($designId !== null && $designId !== '') {
            $baseDesignId = $this->normalizeDesignId($designId);

            if ($this->isAvailableDesign($baseDesignId)) {
                return $this->applyPwaSuffix($baseDesignId, $isPwa);
            }
        }

        $designId = $request->cookie('js_stat_design_id');

        if ($designId !== null && $designId !== '') {
            $baseDesignId = $this->normalizeDesignId($designId);

            if ($this->isAvailableDesign($baseDesignId)) {
                return $this->applyPwaSuffix($baseDesignId, $isPwa);
            }
        }

        $designId = config('app.design');
        $baseDesignId = $this->normalizeDesignId($designId);

        return $this->applyPwaSuffix($baseDesignId, $isPwa);
    }

    private function getCookieLifetime(string $cookieName): int
    {
        return match ($cookieName) {
            'tm_session_id', 'tm_visit_data' => SessionHelper::VISIT_TIMEOUT_MIN,
            default => SessionHelper::DEFAULT_COOKIE_LIFETIME,
        };
    }

    private function isCookieNeeded(Request $request): bool
    {
        return !Str::is(['7-pills.com', '*.7-pills.com', '7-pills.net', '*.7-pills.net', '7-pill.com', '*.7-pill.com', '77-pills.com', '*.77-pills.com', '7-pillz.com', '*.7-pillz.com', '77-pillz.com', '*.77-pillz.com', '777-pills.com', '*.777-pills.com', '777pills.com', '*.777pills.com', '77pillz.com', '*.77pillz.com', '777pillz.com', '*.777pillz.com'], $request->getHost());
    }

    private function isPwaRequest(Request $request): bool
    {
        return $request->query('source') === 'pwa'
            || $request->cookie('is_pwa') === '1'
            || $request->header('X-PWA-Mode') === '1'
            || $request->input('pwa_mode') === '1'
            || $request->input('pwa_mode') === 1;
    }

    private function normalizeDesignId($designId): string
    {
        $designId = trim((string)$designId);

        if (str_starts_with($designId, 'design_')) {
            $designId = substr($designId, strlen('design_'));
        }

        if (str_ends_with($designId, '_pwa')) {
            $designId = substr($designId, 0, -4);
        }

        return $designId;
    }

    private function applyPwaSuffix(string $baseDesignId, bool $isPwa): string
    {
        if ($isPwa) {
            return $baseDesignId . '_pwa';
        }

        return $baseDesignId;
    }

    private function isAvailableDesign(string $designId): bool
    {
        $availableDesigns = array_map('strval', DesignHelper::GetAvailableDesigns());
        return in_array($designId, $availableDesigns, true);
    }
}