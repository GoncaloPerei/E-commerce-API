<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Traits\UpdateCartTrait;
use App\Traits\UpdateUserTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use UpdateUserTrait;

    use UpdateCartTrait;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Payment $payment,
        public array $cart,
        public array $user,
        public $cookie,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->user['money'] > $this->cart['cartPrice']) {
            $this->payment->update([
                'payment_status_id' => 3,
            ]);
            Log::info("User doesn't have money to pay the cart");
            return;
        }

        Log::info("User has money to pay the cart");

        $userBalance = $this->user['money'] - $this->cart['cartPrice'];

        Log::info("User Balance after payment:" . $userBalance);

        $response = $this->updateBalance($userBalance, $this->cookie);

        Log::info($response);

        try {
            $this->payment->update([
                'payment_status_id' => 1,
            ]);

            $this->payment->saveQuietly();
        } catch (QueryException $e) {
            Log::error('Error when updating status' . $e->getMessage());
        }

        Log::info("Payment processed...");
    }
}
