<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetFoods extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetFoods;
        CREATE PROCEDURE trGetFoods()
        BEGIN
            SELECT id, created_by as member_id, food_name, measure, calories,serving_size, serving_unit
            FROM foods WHERE created_by_user_type=1
            ORDER BY id DESC;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetFoods');
    }

}
