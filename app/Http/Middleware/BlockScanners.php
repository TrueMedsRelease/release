<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockScanners
{
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->getRequestUri();           // например: /index.php?s=...
        $qs  = $request->getQueryString() ?? '';    // например: s=/index/\think...

        // 1) Если есть параметр s= (ваш кейс)
        if (preg_match('/(^|&)s=/', $qs)) {
            abort(404);
        }

        if (preg_match('/invokefunction|call_user_func_array|think\\\\app|vars\\[|\\\think\\\/i', $uri . '&' . $qs)) {
            abort(404);
        }

        return $next($request);
    }
}