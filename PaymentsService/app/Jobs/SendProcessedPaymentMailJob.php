<?php

namespace App\Jobs;

use App\Traits\MailTrait;
use App\Traits\PaymentActionsTrait;
use App\Traits\RequestsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

class SendProcessedPaymentMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use MailTrait, RequestsTrait, PaymentActionsTrait;

    protected $user;
    protected $order;
    protected $payment;

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

        try {
            $this->sendProcessedOrderMail($this->user, $this->order, $this->payment);

            Log::info('Email Sent...');
        } catch (\Exception $e) {
            Log::error('An error ocurred when sending email...' . $e);
            return;
        }

        Log::info('Job Finished');
    }
}
