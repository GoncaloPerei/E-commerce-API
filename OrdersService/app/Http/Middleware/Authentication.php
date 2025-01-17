<?php

namespace App\Http\Middleware;

use App\Traits\CookieTrait;
use App\Traits\RequestsTrait;
use Closure;
use Illuminate\Http\Request;

class Authentication
{
    use CookieTrait;
    use RequestsTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $this->getUser($this->getCookie());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Request unauthorized.', 'error' => $e->getMessage()], 403);
        }

        try {
            $request->headers->set('Auth-User', json_encode($response['user']));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error when setting header', 'error' => $e->getMessage()], 403);
        }

        return $next($request);
    }
}
