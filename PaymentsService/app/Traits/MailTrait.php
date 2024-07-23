<?php

namespace App\Traits;

use Illuminate\Support\Facades\Mail;

use App\Mail\ProcessedPaymentMail;

trait MailTrait
{
    public function sendProcessedOrderMail($user, $order, $payment)
    {
        try {
            Mail::to($user['email'])->send(new ProcessedPaymentMail($order, $payment));
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
