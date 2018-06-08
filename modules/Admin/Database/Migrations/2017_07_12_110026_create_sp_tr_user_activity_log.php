<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrUserActivityLog extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trUserActivityLog;
        CREATE PROCEDURE trUserActivityLog(IN memberId INT, IN date DATE)
        BEGIN         
            SELECT id, trimmed_member_id as member_id, activity_type_id, activity, duration, start_time, 
            DATE_FORMAT(activity_date, "%d-%m-%Y") as activity_date,
            activity_source
            FROM trimmed_member_activity_logs 
            where trimmed_member_id=memberId AND activity_date=date
            ORDER BY id DESC;   
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trUserActivityLog');
    }

}
