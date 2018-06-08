<?php namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;

class FoodTypeTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('food_types')->truncate();
        DB::unprepared(file_get_contents(__DIR__ . '/food_types.sql'));
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
