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

    public function updateUserBalance($userBalance, $cookie)
    {
        if ($cookie) {
            return Http::withCookies(['token' => $cookie], 'localhost')->patch('http://localhost:8000/api/profile', [
                'balance' => $userBalance
            ])->json();
        } else {
            return "Cookie not found...";
        }
    }

    public function getOrder($id, $cookie)
    {
        $response = Http::withCookies(['token' => $cookie], 'localhost')->get('http://localhost:8003/api/orders/' . $id)->json();

        if (empty($response)) {
            throw new \Exception("Error when getting order");
        }

        return $response['data'];
    }

    public function getCard($id, $cookie)
    {
        $response = Http::withCookies(['token' => $cookie], 'localhost')->get('http://localhost:8000/api/user/card/' . $id)->json();

        if (empty($response)) {
            throw new \Exception("Error when getting card");
        }

        return $response;
    }

    public function updateCard($balance, $id, $cookie)
    {
        $response = Http::withCookies(['token' => $cookie], 'localhost')->patch('http://localhost:8000/api/user/card/' . $id, [
            'balance' => $balance
        ])->json();

        return $response;
    }

    public function updateProductStock($id, $data)
    {
        $response = Http::patch('http://localhost:8001/api/products/' . $id, ['stock' => $data]);

        return $response;
    }
}
