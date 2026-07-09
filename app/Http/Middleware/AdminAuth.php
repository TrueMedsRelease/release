<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    /**
     * Ensure the caller has an authenticated admin session.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->get('logged_in')) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'text'   => 'Unauthorized',
                    'url'    => route('admin.admin_login'),
                ], 401);
            }

            return redirect()->route('admin.admin_login');
        }

        return $next($request);
    }
}
