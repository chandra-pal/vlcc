<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpCreateReminder extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS createReminder;
        CREATE PROCEDURE createReminder(IN member_id INT,IN reminder_type_id SMALLINT,IN title VARCHAR(50),IN reminder_time TIME,IN reminder_date DATE,IN repeat_type TINYINT, IN repeat_till_date DATE,IN repeat_days VARCHAR(50), OUT reminder_id INT)
        BEGIN
            INSERT INTO member_reminders(member_id, reminder_type_id, title, reminder_time, reminder_date, repeat_type, repeat_till_date, repeat_days, created_at)
            VALUES (member_id, reminder_type_id, title, reminder_time, reminder_date, repeat_type, repeat_till_date, repeat_days, CURRENT_TIMESTAMP());
            SELECT LAST_INSERT_ID() AS reminder_id;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS createReminder');
    }
}
