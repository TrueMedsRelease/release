<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RememberLastPage
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $routeName = $request->route()?->getName();

        $skipRoutes = [
            'home.language',
            'home.language_with_url',
            'home.currency',
            'home.currency_with_url',
            'home.design',
            'home.design_with_url',
            'home.set_images',
            'search.search_autocomplete',
            'cart.content',
            'checkout.content',
            'redirect_url',
        ];

        $contentType = (string) $response->headers->get('Content-Type');

        if (
            $request->isMethod('GET') &&
            ! $request->ajax() &&
            ! $request->expectsJson() &&
            ! in_array($routeName, $skipRoutes, true) &&
            ! $response->isRedirection() &&
            str_contains($contentType, 'text/html')
        ) {
            if ($this->canStoreLastPage($request)) {
                session(['last_page' => $request->fullUrl()]);
            }
        }

        return $response;
    }

    private function canStoreLastPage(Request $request): bool
    {
        if (!$request->isMethod('GET')) {
            return false;
        }

        if ($request->ajax()) {
            return false;
        }

        $path = '/' . ltrim($request->path(), '/');

        $blockedParts = [
            '/lang=',
            '/curr=',
            '/design=',
            '/set_images/',

            '/svg/',
            '/login',
            '/.well-known/',
            '/sw.js',
            '/manifest.json',
            '/manifest.webmanifest',
            '/favicon',
            '/robots.txt',
            '/sitemap',
            '/push/save_push',
            '/pwa/install-event',
            '/debugbar/',
            '/vendor/',
            '/build/',
            '/assets/',
            '/css/',
            '/js/',
            '/storage/',
        ];

        foreach ($blockedParts as $part) {
            if (strpos($path, $part) !== false) {
                return false;
            }
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $blockedExtensions = [
            'svg',
            'png',
            'jpg',
            'jpeg',
            'webp',
            'gif',
            'ico',
            'css',
            'js',
            'map',
            'json',
            'xml',
            'txt',
        ];

        if (in_array($extension, $blockedExtensions, true)) {
            return false;
        }

        return true;
    }
}