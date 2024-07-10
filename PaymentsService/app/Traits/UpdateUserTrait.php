<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait UpdateUserTrait
{
    public function updateBalance($userBalance, $cookie)
    {
        if ($cookie) {
            return Http::withCookies(['token' => $cookie], 'localhost')->patch('http://localhost:8000/api/profile', [
                'balance' => $userBalance
            ])->json();
        } else {
            return "Cookie not found...";
        }
    }
}
