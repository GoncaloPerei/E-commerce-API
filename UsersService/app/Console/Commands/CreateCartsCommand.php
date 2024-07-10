<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

use App\Jobs\CreateCartJob;
use Illuminate\Support\Facades\Log;

class CreateCartsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:create-carts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Command started!");

        try {
            $users = User::get();
        } catch (QueryException $e) {
            Log::error('Failed getting users ' . $e->getMessage());
        }

        foreach ($users as $user) {
            $this->info("Creating cart for {$user->full_name}");
            CreateCartJob::dispatch($user)->delay(2);
            continue;
        }

        $this->info("Command finished!");
    }
}
