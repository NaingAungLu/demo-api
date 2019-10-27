<?php

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
        $this->call(UserTableSeeder::class);
        $this->call(PackageTableSeeder::class);
        $this->call(PromotionTableSeeder::class);
        $this->call(OrderTableSeeder::class);
    }
}
