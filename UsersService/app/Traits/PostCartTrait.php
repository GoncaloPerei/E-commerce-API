<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait PostCartTrait
{
    public function postCart($user)
    {
        return Http::withHeaders([
            'Authorization' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest'
        ])->post('http://localhost:8002/api/cart', $user);
    }
}
