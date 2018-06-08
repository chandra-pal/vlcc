<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetDietDiaryList extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetDietDiaryList;
        CREATE PROCEDURE trGetDietDiaryList(IN memberId INT(10), IN dietDate DATE, IN dietScheduleTypeId SMALLINT(5))
        BEGIN
            SELECT trimmed_member_diet_log.id, trimmed_member_diet_log.trimmed_member_id as member_id, 
            trimmed_member_diet_log.food_name, 
            trimmed_member_diet_log.servings_consumed, 
            trimmed_member_diet_log.diet_schedule_type_id, trimmed_member_diet_log.measure, 
            trimmed_member_diet_log.calories, trimmed_member_diet_log.total_calories, trimmed_member_diet_log.serving_size,
            trimmed_member_diet_log.serving_unit, DATE_FORMAT(trimmed_member_diet_log.diet_date,"%d-%m-%Y") as diet_date, 
            DATE_FORMAT(trimmed_member_diet_log.created_at,"%d-%m-%Y") as created_at,
            trimmed_diet_plan.calories as recommended_calories
            FROM trimmed_member_diet_logs trimmed_member_diet_log
            LEFT OUTER JOIN trimmed_members trimmed_member ON trimmed_member_diet_log.trimmed_member_id = trimmed_member.id
            LEFT OUTER JOIN  trimmed_diet_plans trimmed_diet_plan ON trimmed_member.trimmed_diet_plan_id = trimmed_diet_plan.id            
            WHERE trimmed_member_diet_log.trimmed_member_id = memberId AND trimmed_member_diet_log.diet_date = dietDate AND trimmed_member_diet_log.diet_schedule_type_id = dietScheduleTypeId
            ORDER BY trimmed_member_diet_log.id DESC;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetDietDiaryList');
    }

}
