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
            session(['last_page' => $request->fullUrl()]);
        }

        return $response;
    }
}