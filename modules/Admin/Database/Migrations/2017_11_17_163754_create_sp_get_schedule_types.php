<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetScheduleTypes extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDietScheduleTypes;
        CREATE PROCEDURE getDietScheduleTypes()
        BEGIN
            SELECT id, schedule_name, LOWER(DATE_FORMAT(start_time,"%h:%i %p")) AS start_time 
            FROM diet_schedule_types ORDER BY id ASC;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getDietScheduleTypes');
    }
}
