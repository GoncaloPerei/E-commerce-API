<?php

namespace App\Jobs;

use App\Models\User;
use App\Traits\PostCartTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

class CreateCartJob implements ShouldQueue
{
    use PostCartTrait;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public $cookie)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = $this->postCart($this->user, $this->cookie);
        Log::info('Response ' . $response);
    }
}
