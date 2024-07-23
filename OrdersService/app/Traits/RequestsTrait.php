<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait RequestsTrait
{
    public function getUser($cookie)
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $cookie])->get('http://localhost:8000/api/profile')->json();

        if (empty($response)) {
            throw new \Exception("You Are Not Authenticated");
        }

        return $response['user'];
    }

    public function getCart($cookie)
    {
        //Api request to cart micro
        $response = Http::withCookies(['token' => $cookie], 'localhost')->get('http://localhost:8002/api/cart')->json();

        //Checking if response came empty
        if (empty($response)) {
            throw new \Exception("Cart was not found");
        }

        //Checking if cart is empty
        if (empty($response['data']['cartItem'])) {
            throw new \Exception("Cart doesn't have any items");
        }

        return $response['data'];
    }

    public function cleanCart($cart, $cookie)
    {
        $response = Http::withCookies(['token' => $cookie], 'localhost')->delete('http://localhost:8002/api/cart/' . $cart)->json();

        return $response;
    }

    public function getPayment($id, $cookie)
    {
        $response = Http::withCookies(['token' => $cookie], 'localhost')
            ->get('http://localhost:8004/api/payments/' . $id)
            ->json();

        if (empty($response)) {
            throw new \Exception("Empty request");
        }

        return $response['data'];
    }

    public function processPayment($order, $paymentMethod, $cookie)
    {
        $data = [
            'order' => $order,
            'paymentMethod' => $paymentMethod,
        ];

        $response = Http::withCookies(['token' => $cookie], 'localhost')->post('http://localhost:8004/api/payments', $data)->json();

        return $response;
    }
}
