<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait UpdateCartTrait
{
    public function cleanCart($id, $cookie)
    {
        if ($cookie) {
            return Http::withCookies(['token' => $cookie], 'localhost')->delete('http://localhost:8002/api/cart/' . $id)->json();
        } else {
            return "Cookie not found...";
        }
    }

    public function updateCartStatus()
    {
        //
    }

    public function createCart()
    {
        //
    }
}
