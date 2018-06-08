<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;

class TemporaryTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();

//        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//        DB::table('foods')->truncate();
//        DB::unprepared(file_get_contents(__DIR__ . '/foods.sql'));
//        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

//        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//        DB::table('members')->truncate();
//        DB::unprepared(file_get_contents(__DIR__ . '/members.sql'));
//        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
//        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//        DB::table('member_foods')->truncate();
//        DB::unprepared(file_get_contents(__DIR__ . '/member_foods.sql'));
//        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('diet_plans')->truncate();
        DB::unprepared(file_get_contents(__DIR__ . '/diet_plans.sql'));
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

//        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//        DB::table('member_otp')->truncate();
//        DB::unprepared(file_get_contents(__DIR__ . '/member_otp.sql'));
//        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::unprepared(file_get_contents(__DIR__ . '/temp.sql'));
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('trimmed_diet_plans')->truncate();
        DB::unprepared(file_get_contents(__DIR__ . '/trimmed_diet_plan.sql'));
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
