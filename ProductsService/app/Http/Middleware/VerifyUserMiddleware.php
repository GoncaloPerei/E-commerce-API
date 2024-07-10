<?php

namespace App\Http\Middleware;

use Closure;

class VerifyUserMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $headerData = $request->header('Auth-User');

            if (!$headerData) {
                return response()->json(['message' => 'Request unauthorized.', 'error' => 'You are not authenticated.'], 403);
            }

            $user = json_decode($headerData, true);

            if (!isset($user['role']['id']) || $user['role']['id'] != 1) {
                return response()->json(['message' => 'Request unauthorized.', 'error' => 'You are unauthorized to access this route.'], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
