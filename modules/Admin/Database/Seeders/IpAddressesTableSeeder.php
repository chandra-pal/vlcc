<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;

class IpAddressesTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DB::table('ip_addresses')->truncate();
        DB::unprepared(file_get_contents(__DIR__ . '/ip_addresses.sql'));
    }
}
