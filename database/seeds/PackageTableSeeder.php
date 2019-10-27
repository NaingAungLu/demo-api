x<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

use Carbon\Carbon;

class PackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = "packages";

        $faker = new Faker\Generator();

        $faker->addProvider(new Faker\Provider\Base($faker));
        $faker->addProvider(new Faker\Provider\Uuid($faker));
        $faker->addProvider(new Faker\Provider\Lorem($faker));
        $faker->addProvider(new Faker\Provider\en_US\Person($faker));


        $data = [];

        for ($i = 0; $i < 30; $i++) {

            $data[] = [
                'disp_order' => $faker->numberBetween($min = 1, $max = 30),
                'pack_id' => $faker->uuid($min = 1, $max = 30),
                'pack_name' => $faker->word,
                'pack_description' => $faker->word,
                'pack_type' => $faker->word,
                'total_credit' => $faker->numberBetween($min = 10, $max = 1000),
                'tag_name' => $faker->word,
                'validity_month' => $faker->numberBetween($min = 10, $max = 1000),
                'pack_price' => $faker->numberBetween($min = 10, $max = 1000),
                'newbie_first_attend' => $faker->numberBetween($min = 0, $max = 1),
                'newbie_addition_credit' => $faker->numberBetween($min = 10, $max = 1000),
                'newbie_note' => $faker->word,
                'pack_alias' => $faker->word,
                'estimate_price' => $faker->numberBetween($min = 10, $max = 1000),

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
