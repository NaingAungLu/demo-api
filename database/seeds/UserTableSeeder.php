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

        $data[] = [
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt("password"),
            'status' => Config::get('constants.STATUS.ACTIVE'),
            'created_by' => Config::get('constants.OWNER'),
            'last_updated_by' => Config::get('constants.OWNER'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        DB::table($table_name)->insert($data);
    }
}
