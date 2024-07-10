<?php

namespace App\Traits;

use App\Models\OrderDetails;

trait CRUDDetailsTrait
{
    public function getDetails()
    {

    }

    public function storeDetails($details)
    {
        return OrderDetails::create($details);
    }

    public function updateDetails()
    {

    }
}
