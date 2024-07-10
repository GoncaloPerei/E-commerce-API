<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Log;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            User::factory()->create([
                'full_name' => 'Administrator Administrator',
                'email' => 'administrator@administrator.com',
                'password' => 'administrator',
                'role_id' => '1',
                'money' => 999999999,
            ]);

            User::factory()
                ->count(50)
                ->create();

            DB::commit();
            echo 'Success! |';
        } catch (QueryException $e) {
            Log::error('Error' . $e);
            DB::rollBack();
            echo 'Error! |';
        }
    }
}
