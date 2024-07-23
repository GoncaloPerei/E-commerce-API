<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;

use App\Models\PaymentType;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            PaymentType::factory()->create([
                'name' => 'E-commerce Balance',
                'description' => "This payment method withdraws money in the e-commerce balance account"
            ]);
            PaymentType::factory()->create([
                'name' => 'Debit or Credit Card',
                'description' => "This payment method withdraws money from a debit or credit card"
            ]);

            DB::commit();
            echo 'Success! |';
        } catch (QueryException $e) {
            DB::rollBack();
            echo 'Error! |';
        }
    }
}
