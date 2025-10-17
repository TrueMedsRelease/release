<?php

namespace App\Http\Middleware;

use App\Helpers\DesignHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetCookiesForStatistics
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$this->isCookieNeeded($request)) {
            return $response;
        }

        $cookieList = [
            'js_stat_aff_id'    => $this->getAffiliateId($request),
            'js_stat_design_id' => $this->getDesignId($request),
        ];

        foreach ($cookieList as $name => $value) {
            if (!$request->hasCookie($name) || $request->cookie($name) != $value) {
                $cookie = cookie(
                    name: $name,
                    value: $value,
                    minutes: 365 * 24 * 60, // 365 days
                    path: '/',
                    secure: true,
                    httpOnly: false, // set true if JS should NOT read it
                    sameSite: 'lax'
                );

                $response->headers->setCookie($cookie);
            }
        }

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

    private function isCookieNeeded(Request $request): bool
    {
        return !Str::endsWith($request->getHost(), '7-pills.com');
    }
}
