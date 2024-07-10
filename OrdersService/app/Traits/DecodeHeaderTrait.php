<?php

namespace App\Traits;

trait DecodeHeaderTrait
{
    public function decodeHeader($request)
    {
        $headerData = $request->header('Auth-User');

        return json_decode($headerData, true);
    }
}
