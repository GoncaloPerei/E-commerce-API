<?php

namespace App\Jobs;

use App\Models\Order;
use App\Traits\MailTrait;
use App\Traits\RequestsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

class SendConfirmOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use RequestsTrait, MailTrait;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order,
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
            $this->sendConfirmOrderMail($this->user, $this->order);

            Log::info('Email Sent...');
        } catch (\Exception $e) {
            Log::error('An error ocurred when sending email...' . $e);
            return;
        }

        Log::info('Job Finished');
    }
}
