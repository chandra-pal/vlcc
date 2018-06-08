<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetUserDietPlanDetails extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetUserDietPlanDetail;        
        CREATE PROCEDURE trGetUserDietPlanDetail(IN memberId INT)     
        BEGIN           
            SELECT D.plan_type, D.calories 
            FROM trimmed_diet_plans D INNER JOIN trimmed_members M 
            ON D.id = M.trimmed_diet_plan_id
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
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetUserDietPlanDetail');
    }

}
