<?php

namespace App\Traits;

trait DecodeAuthHeaderTrait
{
    public function decodeHeader($request)
    {
        $headerData = $request->header('Auth-User');

        return json_decode($headerData);
    }
}
