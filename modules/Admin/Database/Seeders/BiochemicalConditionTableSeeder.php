<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;

class BiochemicalConditionTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('biochemical_condition')->truncate();
        DB::unprepared(file_get_contents(__DIR__ . '/biochemical_condition.sql'));
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
