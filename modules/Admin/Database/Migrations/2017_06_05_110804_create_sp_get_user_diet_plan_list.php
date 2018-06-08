<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetUserDietPlanList extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getUserDietPlanList;        
        CREATE PROCEDURE getUserDietPlanList(IN memberId INT)     
        BEGIN           
            SELECT F.id, F.food_name, F.measure, F.calories, F.serving_size, F.serving_unit, D.servings_recommended, D.diet_plan_id, D.diet_schedule_type_id
            FROM member_diet_plan_details D
            LEFT JOIN foods F ON F.id=D.food_id
            LEFT JOIN members M ON M.id=D.member_id
            WHERE M.id = memberId;
        END      
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getUserDietPlanList');
    }

}
