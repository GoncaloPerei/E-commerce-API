<?php

namespace App\Traits;

use App\Models\Payment;

use App\Http\Resources\PaymentResource;

trait PaymentActionsTrait
{
    public function getPayment($id)
    {
        $data = Payment::where('id', $id)
            ->with('status', 'type')
            ->firstOrFail();

        return new PaymentResource($data);
    }
}
