<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(CategorySeeder::class);
        $this->call(ItemSeeder::class);
        $this->call(TransactionSeeder::class);
        //$this->call(EvaluationSeeder::class);
        //評価のシーダーは必要な時に使用してください
    }
}
