<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetRecommendedCalories extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetRecommendedCalories;
        CREATE PROCEDURE trGetRecommendedCalories(IN memberId INT(10))
        BEGIN
            SELECT 
            trimmed_diet_plan.calories AS recommended_calories
            FROM trimmed_members trimmed_member
            LEFT OUTER JOIN trimmed_diet_plans trimmed_diet_plan ON
            trimmed_member.trimmed_diet_plan_id = trimmed_diet_plan.id
            WHERE trimmed_member.id = memberId;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetRecommendedCalories');
    }

}
