<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetUserDietPlanDetail extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getUserDietPlanDetail;        
        CREATE PROCEDURE getUserDietPlanDetail(IN memberId INT)     
        BEGIN           
            SELECT D.plan_type, D.calories 
            FROM diet_plans D INNER JOIN members M 
            ON D.id = M.diet_plan_id
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getUserDietPlanDetail');
    }

}
