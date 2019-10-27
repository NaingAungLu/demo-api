x<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = "users";

        $faker = new Faker\Generator();

        $faker->addProvider(new Faker\Provider\Internet($faker));
        $faker->addProvider(new Faker\Provider\en_US\Person($faker));

        $data = [];

        for ($i = 0; $i < 30; $i++) {

            $data[] = [
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => bcrypt("password"),
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
