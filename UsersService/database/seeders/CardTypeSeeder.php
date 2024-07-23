<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;

use App\Models\CardType;

class CardTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            CardType::factory()->create([
                'name' => 'Mastercard',
                'image' => 'https://static-00.iconduck.com/assets.00/mastercard-icon-512x329-xpgofnyv.png',
            ]);
            CardType::factory()->create([
                'name' => 'Visa',
                'image' => 'https://static-00.iconduck.com/assets.00/visa-icon-512x329-mpibmtt8.png',
            ]);

            DB::commit();
            echo 'Success! |';
        } catch (QueryException $e) {
            DB::rollBack();
            echo 'Error! |';
        }
    }
}
