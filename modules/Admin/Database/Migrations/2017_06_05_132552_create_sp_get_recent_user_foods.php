<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetRecentUserFoods extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getRecentUserFoods;
        CREATE PROCEDURE getRecentUserFoods(IN memberId INT(10))
        BEGIN
            SELECT D.id, D.member_id, D.food_name, D.servings_consumed, D.diet_schedule_type_id,
            D.measure, D.calories, D.total_calories, D.serving_size, D.serving_unit,
            DATE_FORMAT(D.diet_date,"%d-%m-%Y") as diet_date,
            DATE_FORMAT(D.created_at,"%d-%m-%Y") as created_at,
            F.food_type_id, F.food_name as food_type_name
            FROM member_diet_logs D
            INNER JOIN foods F ON D.food_id=F.id
            WHERE D.member_id = memberId
            GROUP BY D.food_id
            ORDER BY D.diet_date DESC;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getRecentUserFoods');
    }

}
