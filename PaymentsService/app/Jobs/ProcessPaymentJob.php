<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

use App\Jobs\SendProcessedPaymentMailJob;
use App\Traits\PaymentActionsTrait;
use App\Traits\RequestsTrait;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use RequestsTrait, PaymentActionsTrait;

    protected $user;
    protected $order;
    protected $payment;
    protected $cart;
    protected $card;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $paymentId,
        public $cookie,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->user = $this->getUser($this->cookie);
        } catch (\Exception $e) {
            Log::error('An error ocurred when getting user... ' . $e);
            return;
        }

        try {
            $this->payment = $this->getPayment($this->paymentId, $this->cookie);
        } catch (\Exception $e) {
            Log::error($e);
            return;
        }

        try {
            $this->order = $this->getOrder($this->payment['order_id'], $this->cookie);
        } catch (\Exception $e) {
            Log::error($e);
            return;
        }

        $this->cart = $this->order['cart'];

        Log::info($this->cart);

        foreach ($this->cart['cartItem'] as $item) {
            if ($item['product']['stock'] >= $item['quantity']) {
                Log::info("{$item['product']['title']} has stock");
            } else {
                Log::info("{$item['product']['title']} does not have stock");

                try {
                    $this->payment->update([
                        'payment_status_id' => 3,
                        'notes' => 'Product does not have enough stock',
                    ]);

                    $this->payment->saveQuietly();
                } catch (QueryException $e) {
                    Log::error('Error when updating status' . $e->getMessage());
                }

                return;
            }
        }

        switch ($this->payment['payment_type_id']) {
            case 1:
                Log::info('E-commerce Balance');

                if ($this->user['balance'] < $this->cart['cartPrice']) {
                    $error = 'E-commerce Balance does not have enough money to pay the cart';

                    Log::error($error);

                    try {
                        $this->payment->update([
                            'payment_status_id' => 3,
                            'notes' => 'E-commerce Balance does not have enough money to pay the cart',
                        ]);

                        $this->payment->saveQuietly();
                    } catch (QueryException $e) {
                        Log::error('Error when updating status' . $e->getMessage());
                    }

                    break;
                }

                Log::info('E-commerce Balance has money to pay the cart');

                $userBalance = $this->user['balance'] - $this->cart['cartPrice'];

                Log::info("E-commerce Balance after payment:" . $userBalance);

                try {
                    $response = $this->updateUserBalance($userBalance, $this->cookie);
                } catch (\Exception $e) {
                    Log::error($e);
                    break;
                }

                Log::info($response);

                try {
                    $this->payment->update([
                        'payment_status_id' => 1,
                        'notes' => 'Paid with E-commerce Balance'
                    ]);

                    $this->payment->saveQuietly();
                } catch (QueryException $e) {
                    Log::error('Error when updating status' . $e->getMessage());
                }

                foreach ($this->cart['cartItem'] as $item) {
                    $stock = $item['product']['stock'] - $item['quantity'];

                    $response = $this->updateProductStock($item['product']['id'], $stock);

                    Log::info($response);
                }

                Log::info("Payment accepted...");

                break;
            case 2:
                Log::info('Credit or Debit Card');

                $response = $this->getCard($this->payment['card_id'], $this->cookie);

                $this->card = $response['data'];

                $currentDate = new \DateTime();
                $currentYear = substr($currentDate->format('Y'), -2);
                $currentMonth = $currentDate->format('m');

                $expirationMonth = substr($this->card['expirationDate'], 0, 2);
                $expirationYear = substr($this->card['expirationDate'], 3, 4);

                if ($expirationYear < $currentYear || ($expirationYear == $currentYear && $expirationMonth < $currentMonth)) {
                    $error = 'Card is expired';

                    Log::error($error);

                    try {
                        $this->payment->update([
                            'payment_status_id' => 3,
                            'notes' => 'Card is expired',
                        ]);

                        $this->payment->saveQuietly();
                    } catch (QueryException $e) {
                        Log::error('Error when updating status' . $e->getMessage());
                    }

                    break;
                }

                Log::info("Card is valid");

                if ($this->card['balance'] < $this->cart['cartPrice']) {
                    $error = 'Card does not have enough money to pay the cart';

                    Log::error($error);

                    try {
                        $this->payment->update([
                            'payment_status_id' => 3,
                            'notes' => 'Card does not have enough money to pay the cart',
                        ]);

                        $this->payment->saveQuietly();
                    } catch (QueryException $e) {
                        Log::error('Error when updating status' . $e->getMessage());
                    }

                    break;
                }

                Log::info('Card has money to pay the cart');

                $cardBalance = $this->card['balance'] - $this->cart['cartPrice'];

                $response = $this->updateCard($cardBalance, $this->card['id'], $this->cookie);

                Log::info($response);

                try {
                    $this->payment->update([
                        'payment_status_id' => 1,
                        'notes' => 'Paid with cart'
                    ]);

                    $this->payment->saveQuietly();
                } catch (QueryException $e) {
                    Log::error('Error when updating status' . $e->getMessage());
                }

                foreach ($this->cart['cartItem'] as $item) {
                    $stock = $item['product']['stock'] - $item['quantity'];

                    $response = $this->updateProductStock($item['product']['id'], $stock);

                    Log::info($response);
                }

                Log::info("Payment accepted...");

                break;
        }

        SendProcessedPaymentMailJob::dispatch($this->paymentId, $this->cookie);

        Log::info("Payment processed...");
    }
}
