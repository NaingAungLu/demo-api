x<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

use Carbon\Carbon;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = "orders";

        $faker = new Faker\Generator();

        $faker->addProvider(new Faker\Provider\Base($faker));

        $data = [];

        for ($i = 0; $i < 30; $i++) {

            $data[] = [
                'package_id' => $faker->numberBetween($min = 1, $max = 30),
                'order_date' => Carbon::now(),
                'grand_total' => $faker->numberBetween($min = 1000, $max = 100000),

                'status' => Config::get('constants.STATUS.ACTIVE'),
                'created_by' => Config::get('constants.OWNER'),
                'last_updated_by' => Config::get('constants.OWNER'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        DB::table($table_name)->insert($data);
    }
}
