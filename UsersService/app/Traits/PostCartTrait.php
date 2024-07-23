<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait PostCartTrait
{
    public function postCart($user, $cookie)
    {
        return Http::withHeaders([
            'Authorization' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest'
        ])->withCookies(['token' => $cookie], 'localhost')->post('http://localhost:8002/api/cart', $user);
    }
}
