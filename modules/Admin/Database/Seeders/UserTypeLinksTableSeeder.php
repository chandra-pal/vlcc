<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;

class UserTypeLinksTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DB::table('user_type_links')->truncate();
        DB::unprepared(file_get_contents(__DIR__ . '/user_type_links.sql'));
    }
}
