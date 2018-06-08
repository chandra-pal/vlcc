<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetFrequentUserFoods extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getFrequentUserFoods;
        CREATE PROCEDURE getFrequentUserFoods(IN memberId INT(10))
        BEGIN
            SELECT D.id, D.member_id, COUNT(D.food_id) as food_occurrence, D.food_name, D.measure, D.calories, D.serving_size, D.serving_unit, F.food_type_id, F.food_name as food_type_name
            FROM member_diet_logs D
            INNER JOIN
            foods F ON D.food_id=F.id
            WHERE D.member_id = memberId
            GROUP BY D.food_id
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getFrequentUserFoods');
    }

}
