<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyAuthMiddleware
{
    /**
     * Проверяет статический API-ключ в заголовке X-API-KEY.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey || $apiKey !== config('api.key')) {
            return response()->json(['error' => 'Unauthorized: Invalid or missing API key'], 401);
        }

        return $next($request);
    }
}
