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

        $faker->addProvider(new Faker\Provider\Internet($faker));
        $faker->addProvider(new Faker\Provider\en_US\Person($faker));

        $data = [];

        for ($i = 0; $i < 30; $i++) {

            $data[] = [
                'disp_order' => 1,
                'pack_id' => 1,
                'pack_name' => 1,
                'pack_description' => 1,
                'pack_type' => 1,
                'total_credit' => 1,
                'tag_name' => 1,
                'validity_month' => 1,
                'pack_price' => 1,
                'newbie_first_attend' => 1,
                'newbie_addition_credit' => 1,
                'newbie_note' => 1,
                'pack_alias' => 1,
                'estimate_price' => 1,

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
