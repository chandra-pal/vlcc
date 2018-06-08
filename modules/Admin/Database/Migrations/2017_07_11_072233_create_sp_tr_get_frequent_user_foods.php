<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetFrequentUserFoods extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetFrequentUserFoods;
        CREATE PROCEDURE trGetFrequentUserFoods(IN memberId INT(10))
        BEGIN
            SELECT id, trimmed_member_id, COUNT(food_name) as food_occurrence, food_name, measure, calories, serving_size, serving_unit
            FROM trimmed_member_diet_logs
            WHERE trimmed_member_id = memberId
            GROUP BY food_name
            ORDER BY food_occurrence DESC;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetFrequentUserFoods');
    }

}
