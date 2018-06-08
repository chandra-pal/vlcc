<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetDietDiaryList extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDietDiaryList;
        CREATE PROCEDURE getDietDiaryList(IN memberId INT(10), IN dietDate DATE, IN dietScheduleTypeId SMALLINT(5))
        BEGIN
            SELECT member_diet_log.id, member_diet_log.member_id, member_diet_log.food_name, 
            member_diet_log.servings_consumed, member_diet_log.diet_schedule_type_id, member_diet_log.measure, 
            member_diet_log.calories, member_diet_log.total_calories, member_diet_log.serving_size,
            member_diet_log.serving_unit, DATE_FORMAT(member_diet_log.diet_date,"%d-%m-%Y") as diet_date, 
            DATE_FORMAT(member_diet_log.created_at,"%d-%m-%Y") as created_at,
            diet_plan.calories as recommended_calories
            FROM member_diet_logs member_diet_log
            LEFT OUTER JOIN members member ON member_diet_log.member_id = member.id
            LEFT OUTER JOIN  diet_plans diet_plan ON member.diet_plan_id = diet_plan.id            
            WHERE member_diet_log.member_id = memberId AND member_diet_log.diet_date = dietDate AND member_diet_log.diet_schedule_type_id = dietScheduleTypeId
            ORDER BY member_diet_log.id DESC;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getDietDiaryList');
    }

}
