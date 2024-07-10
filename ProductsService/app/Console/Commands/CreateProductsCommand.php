<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\CreateProductsJob;
use App\Traits\FakeStoreApiTrait;

use Illuminate\Support\Facades\Log;

class CreateProductsCommand extends Command
{
    use FakeStoreApiTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:create-products';

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
            $products = $this->fetchProductsFromApi();
        } catch (\Exception $e) {
            Log::error('Failed to fetch products ' . $e->getMessage());
            $this->error('Failed to fetch products');
            return $this->info("Command finished!");
        }

        foreach ($products as $product) {
            $this->info("Creating product: {$product['title']}");
            try {
                CreateProductsJob::dispatch($product)->delay(2);
            } catch (\Exception $e) {
                Log::error("Failed to create product: {$product['title']}" . $e->getMessage());
                $this->error("Failed to create product: {$product['title']}");
            }
            continue;
        }

        return $this->info("Command finished!");
    }
}
