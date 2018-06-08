<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetRecommendedCalories extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getRecommendedCalories;
        CREATE PROCEDURE getRecommendedCalories(IN memberId INT(10))
        BEGIN
            SELECT 
            diet_plan.calories AS recommended_calories
            FROM members member
            LEFT OUTER JOIN diet_plans diet_plan ON
            member.diet_plan_id = diet_plan.id
            WHERE member.id = memberId;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getRecommendedCalories');
    }

}
