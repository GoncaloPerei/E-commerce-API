<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            ProductCategory::factory()->create([
                'name' => 'Electronics',
                'image' => 'https://s1.kuantokusta.pt/img_upload/produtos_electrodomesticos_familias/168/168.jpg'
            ]);
            ProductCategory::factory()->create([
                'name' => "Jewelery",
                'image' => 'https://s1.kuantokusta.pt/img_upload/produtos_modacessorios_familias/117/117.jpg'
            ]);
            ProductCategory::factory()->create([
                'name' => "Men's clothing",
                'image' => 'https://s1.kuantokusta.pt/img_upload/produtos_modacessorios_familias/12/12.jpg'
            ]);
            ProductCategory::factory()->create([
                'name' => "Women's clothing",
                'image' => 'https://s1.kuantokusta.pt/img_upload/produtos_modacessorios_familias/1/1.jpg'
            ]);

            DB::commit();
            echo 'Success! |';
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Product Category Seeder Failed: ' . $e);
            echo 'Error! |';
        }
    }
}
