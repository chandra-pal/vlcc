<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetUserDietPlanList extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetUserDietPlanList;        
        CREATE PROCEDURE trGetUserDietPlanList(IN memberId INT)     
        BEGIN           
            SELECT F.food_name, F.measure, F.calories, F.serving_size, F.serving_unit, D.servings_recommended, D.trimmed_diet_plan_id, D.diet_schedule_type_id
            FROM trimmed_members M
            LEFT JOIN trimmed_diet_plan_details D ON M.trimmed_diet_plan_id = D.trimmed_diet_plan_id
            INNER JOIN foods F ON D.food_id = F.id
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
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetUserDietPlanList');
    }

}
