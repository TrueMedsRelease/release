<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RememberLastPage
{
    public function handle(Request $request, Closure $next)
    {
        if (
            $request->isMethod('GET') &&
            ! $request->ajax() &&
            ! $request->expectsJson() &&
            str_contains($request->header('accept', ''), 'text/html') &&
            ! $request->is('set_images/*') &&
            ! $request->is('currency/*') &&
            ! $request->is('language/*')
        ) {
            session(['last_page' => $request->fullUrl()]);
        }

        return $next($request);
    }
}