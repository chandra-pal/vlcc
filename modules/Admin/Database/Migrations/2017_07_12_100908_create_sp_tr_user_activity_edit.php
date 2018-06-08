<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrUserActivityEdit extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trUserActivityEdit;        
        CREATE PROCEDURE trUserActivityEdit(IN memberId INT,IN date DATE,IN activity VARCHAR(50),IN duration SMALLINT(5),IN start_time TIME, IN activityID INT)     
        BEGIN 
            UPDATE trimmed_member_activity_logs
            SET trimmed_member_id=memberId, activity=activity, duration=duration, start_time=start_time, activity_date=date, updated_at=CURRENT_TIMESTAMP()
            WHERE id=activityID;     
        END       
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trUserActivityEdit');
    }

}
