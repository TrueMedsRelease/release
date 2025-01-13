<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AdvancedSqlInjectionChecker;

class CheckForSqlInjection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $allInputs = $request->all();//array_merge($request->all(), $request->route()->parameters());

        foreach ($allInputs as $key => $value) {
            $count = substr_count($value, '\'');
            if ($key == 'push_info' || ($key == 'search' && $count == 1)) {
                continue;
            } else {
                if (is_string($value) && AdvancedSqlInjectionChecker::hasSqlInjection($value)) {
                    return response()->json(['error' => 'Potential SQL Injection detected in parameter: ' . $key], 400);
                }
            }
        }

        return $next($request);
    }
}
