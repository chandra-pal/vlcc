<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetRecentUserFoods extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetRecentUserFoods;
        CREATE PROCEDURE trGetRecentUserFoods(IN memberId INT(10))
        BEGIN
            SELECT id, trimmed_member_id as member_id, food_name, servings_consumed, diet_schedule_type_id, 
            measure, calories, total_calories, serving_size, serving_unit, 
            DATE_FORMAT(diet_date,"%d-%m-%Y") as diet_date, 
            DATE_FORMAT(created_at,"%d-%m-%Y") as created_at
            FROM trimmed_member_diet_logs 
            WHERE trimmed_member_id = memberId
            GROUP BY food_name
            ORDER BY diet_date DESC;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetRecentUserFoods');
    }

}
