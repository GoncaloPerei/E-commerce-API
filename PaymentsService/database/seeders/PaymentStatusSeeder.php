<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;

use App\Models\PaymentStatus;

class PaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            PaymentStatus::factory()->create([
                'name' => 'Accepted',
            ]);
            PaymentStatus::factory()->create([
                'name' => 'Pending',
            ]);
            PaymentStatus::factory()->create([
                'name' => 'Denied',
            ]);

            DB::commit();
            echo 'Success! |';
        } catch (QueryException $e) {
            DB::rollBack();
            echo 'Error! |';
        }
    }
}
