<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetLatestActivity extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getLatestActivity;
        CREATE PROCEDURE getLatestActivity(IN memberId INT(10))
        BEGIN 
            SELECT activity, duration, start_time, DATE_FORMAT(activity_date,"%d-%m-%Y") as activity_date
            FROM member_activity_logs
            WHERE member_id = memberId AND 
            activity_date = CURDATE()
            ORDER BY CONCAT(activity_date," ",start_time) DESC
            LIMIT 1;     
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getLatestActivity');
    }

}
