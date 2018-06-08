<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetActivityTypes extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getActivityTypeList;
        CREATE PROCEDURE getActivityTypeList()
        BEGIN
            SELECT id, activity_type, status
            FROM activity_types
            WHERE status = 1 AND id!=99;            
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getActivityTypeList');
    }
}
