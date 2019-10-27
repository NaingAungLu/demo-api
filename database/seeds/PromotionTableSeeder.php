x<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

use Carbon\Carbon;

class PromotionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = "promotions";

        $faker = new Faker\Generator();

        $faker->addProvider(new Faker\Provider\Base($faker));

        $data = [];

        for ($i = 0; $i < 30; $i++) {

            $data[] = [
                'promo_code' => $faker->randomNumber,
                'amount' => $faker->numberBetween($min = 100, $max = 10000),
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now(),

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
