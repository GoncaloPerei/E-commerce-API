<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;

class Authentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = Cookie::get("token");

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get('http://localhost:8000/api/profile');

        if (empty($response->json())) {
            return response()->json(['message' => 'Request Unauthorized.', 'error' => 'You Are Not Authenticated.'], 403);
        }

        $request->headers->set('Auth-User', json_encode($response->json(['user'])));

        return $next($request);
    }
}
