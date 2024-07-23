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

    public function getProduct($id)
    {
        $response = Http::get('http://localhost:8001/api/products/' . $id);

        if (empty($response)) {
            throw new \Exception("Product Not Found");
        }

        return $response['data'];
    }
}
