<?php

namespace App\Jobs;

use App\Models\Order;
use App\Traits\RequestsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrderJob implements ShouldQueue
{
    use RequestsTrait;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order,
        public array $cart,
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
            $response = $this->cleanCart($this->cart['id'], $this->cookie);

            Log::info($response);
        } catch (\Exception $e) {
            Log::error('Error cleaning cart...' . $e);
        }

        try {
            
        } catch (\Exception $e) {
            Log::error('Error processing payment...' . $e);
        }
    }
}
