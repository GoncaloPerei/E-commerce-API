<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;

use App\Traits\CookieTrait;
use App\Traits\RequestsTrait;

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

        if (empty($response)) {
            return response()->json(['message' => 'Request unauthorized.', 'error' => 'User came empty']);
        }

        return $next($request);
    }
}
