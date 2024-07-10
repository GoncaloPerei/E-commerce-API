<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait FakeStoreApiTrait
{
    //Função que busca produtos na fake store api
    public function fetchProductsFromApi()
    {
        return Http::get('https://fakestoreapi.com/products')->json();
    }
}
