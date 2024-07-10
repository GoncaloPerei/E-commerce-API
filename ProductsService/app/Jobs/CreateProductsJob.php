<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductCategory;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

class CreateProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $product)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $category = ProductCategory::where('name', $this->product['category'])->firstOrFail();

        try {
            Product::updateOrCreate(
                ['id' => $this->product['id']],
                [
                    'title' => $this->product['title'],
                    'description' => $this->product['description'],
                    'price' => $this->product['price'],
                    'image' => $this->product['image'],
                    'stock' => random_int(0, 100),
                    'product_category_id' => $category->id,
                ]
            );
            Log::info('Product created successfully');
        } catch (QueryException $e) {
            Log::error('Failed to create product' . $e->getMessage());
        }
    }
}
