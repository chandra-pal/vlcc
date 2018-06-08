<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetDeviations extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS getDeviations;
            CREATE PROCEDURE getDeviations(IN memberId INT, IN dietScheduleTypeId INT, IN deviationDate DATE)
            BEGIN
                SELECT calories_recommended, calories_consumed, (calories_recommended - calories_consumed) AS deviation 
                FROM member_diet_deviations
                WHERE member_id = memberId AND diet_schedule_type_id = dietScheduleTypeId AND deviation_date = deviationDate;
            END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDeviations;');
    }

}
