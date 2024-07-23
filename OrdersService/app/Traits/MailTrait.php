<?php

namespace App\Traits;

use Illuminate\Support\Facades\Mail;

use App\Mail\ConfirmOrderMail;

trait MailTrait
{
    public function sendConfirmOrderMail($user, $order)
    {
        try {
            Mail::to($user['email'])->send(new ConfirmOrderMail($order));
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
