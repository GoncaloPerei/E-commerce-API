<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Mail\ConfirmOrderMail;
use Illuminate\Support\Facades\Mail;

use App\Models\Order;

class MailTest extends TestCase
{

    public function test_email_via_show_order()
    {
        $order = Order::where('id', 1)->firstOrFail();
        
        $this->get('api/test/mail', $order);

        Mail::fake();

        Mail::assertSent(ConfirmOrderMail::class);
    }
}
