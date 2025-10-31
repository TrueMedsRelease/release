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
                'signature' => $visitData['signature']
            ]),
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
                    secure: true,
                    httpOnly: false, // false = JavaScript can read cookie
                    sameSite: 'lax'
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
        if ($aff) {
            return $aff;
        }

        $aff = $request->cookie('AFF_ID');
        if ($aff) {
            return $aff;
        }

        $aff = $request->cookie('js_stat_aff_id');
        if ($aff) {
            return $aff;
        }

        return config('app.aff');
    }

    private function getDesignId(Request $request)
    {
        $designId = (int)$request->query('design');
        if (in_array($designId, DesignHelper::GetAvailableDesigns(), true)) {
            return $designId;
        }

        $designId = $request->cookie('js_stat_design_id');
        if ($designId) {
            return $designId;
        }

        $designId = config('app.design');
        if (is_string($designId)) {
            return str_replace('design_', '', $designId);
        }

        return config('app.design');
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
        return !Str::endsWith($request->getHost(), '7-pills.com');
    }
}