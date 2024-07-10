<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cookie;

trait CookieTrait
{

    public function getCookie()
    {
        return Cookie::get("token");
    }
}
