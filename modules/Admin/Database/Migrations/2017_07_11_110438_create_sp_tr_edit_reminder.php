<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrEditReminder extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trEditReminder;
        CREATE PROCEDURE trEditReminder(IN id INT, IN member_id INT,IN reminder_type_id SMALLINT,IN title VARCHAR(50),IN reminder_time TIME,IN reminder_date DATE,IN repeat_type TINYINT, IN repeat_till_date DATE,IN repeat_days VARCHAR(50))
        BEGIN
            UPDATE trimmed_member_reminders 
            SET reminder_type_id = reminder_type_id, title =  title, reminder_time = reminder_time, reminder_date = reminder_date, repeat_type = repeat_type, repeat_till_date = repeat_till_date, repeat_days = repeat_days, updated_at = CURRENT_TIMESTAMP() WHERE id = id AND trimmed_member_id = member_id;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trEditReminder');
    }

}
