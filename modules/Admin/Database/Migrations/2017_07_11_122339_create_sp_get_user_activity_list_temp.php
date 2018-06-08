<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetUserActivityListTemp extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS userActivityLog;
        CREATE PROCEDURE userActivityLog(IN memberId INT, IN date DATE)
        BEGIN         
            SELECT id, member_id, activity_type_id, activity, duration, start_time, 
            DATE_FORMAT(activity_date, "%d-%m-%Y") as activity_date,
            activity_source
            FROM member_activity_logs 
            where member_id=memberId AND activity_date=date
            ORDER BY id DESC;   
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
        DB::unprepared('DROP PROCEDURE IF EXISTS userActivityLog');
    }

}
