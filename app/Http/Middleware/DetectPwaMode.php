<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DetectPwaMode
{
    public function handle(Request $request, Closure $next)
    {
        $isPwa = false;
        $cookies = [];

        foreach (explode(';', $request->header('cookie')) as $cookie) {
            $parts = explode('=', trim($cookie), 2);

            if (count($parts) === 2) {
                $name = trim($parts[0]);
                $value = urldecode($parts[1]);

                $cookies[$name] = $value;
            }
        }

        if ((isset($cookies['is_pwa']) && $cookies['is_pwa'] == '1') || $request->query('source') === 'pwa') {
            session(['is_pwa' => 1]);
            $isPwa = true;
        } else {
            session(['is_pwa' => 0]);
        }

        view()->share('isPwa', $isPwa);

        return $next($request);
    }
}