<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetReminderList extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetReminderList;
        CREATE PROCEDURE trGetReminderList(IN memberId INT(10), IN reminderTypeId INT)
        BEGIN
            IF(reminderTypeId > 0) THEN
            
            SELECT m1.id, m1.trimmed_member_id as member_id, m1.reminder_type_id, m1.title, m1.reminder_time, DATE_FORMAT(m1.reminder_date,"%d-%m-%Y")as reminder_date, repeat_type, DATE_FORMAT(m1.repeat_till_date,"%d-%m-%Y") as repeat_till_date, m1.repeat_days FROM trimmed_member_reminders m1 WHERE m1.repeat_type = 1 AND  m1.trimmed_member_id = memberId AND m1.reminder_type_id = reminderTypeId
            UNION
            SELECT m2.id, m2.trimmed_member_id as member_id, m2.reminder_type_id, m2.title, m2.reminder_time, DATE_FORMAT(m2.reminder_date,"%d-%m-%Y")as reminder_date, repeat_type, DATE_FORMAT(m2.repeat_till_date,"%d-%m-%Y") as repeat_till_date, m2.repeat_days FROM trimmed_member_reminders m2 WHERE m2.repeat_type =0  AND CURRENT_TIMESTAMP() <= CONCAT(m2.reminder_date," ",m2.reminder_time) AND m2.trimmed_member_id = memberId AND m2.reminder_type_id = reminderTypeId
            UNION 
            SELECT m3.id, m3.trimmed_member_id as member_id, m3.reminder_type_id, m3.title, m3.reminder_time, DATE_FORMAT(m3.reminder_date,"%d-%m-%Y") as reminder_date, repeat_type, DATE_FORMAT(m3.repeat_till_date,"%d-%m-%Y") as repeat_till_date, m3.repeat_days 
            FROM trimmed_member_reminders m3 WHERE m3.repeat_type =2  AND CURRENT_TIMESTAMP() <= CONCAT(m3.repeat_till_date," ",m3.reminder_time) AND  m3.trimmed_member_id = memberId AND m3.reminder_type_id = reminderTypeId;
            
            ELSE 
            
            SELECT m1.id, m1.trimmed_member_id as member_id, m1.reminder_type_id, m1.title, m1.reminder_time, DATE_FORMAT(m1.reminder_date,"%d-%m-%Y")as reminder_date, repeat_type, DATE_FORMAT(m1.repeat_till_date,"%d-%m-%Y") as repeat_till_date, m1.repeat_days FROM trimmed_member_reminders m1 WHERE m1.repeat_type = 1 AND  m1.trimmed_member_id = memberId 
            UNION
            SELECT m2.id, m2.trimmed_member_id as member_id, m2.reminder_type_id, m2.title, m2.reminder_time, DATE_FORMAT(m2.reminder_date,"%d-%m-%Y")as reminder_date, repeat_type, DATE_FORMAT(m2.repeat_till_date,"%d-%m-%Y") as repeat_till_date, m2.repeat_days FROM trimmed_member_reminders m2 WHERE m2.repeat_type =0  AND CURRENT_TIMESTAMP() <= CONCAT(m2.reminder_date," ",m2.reminder_time) AND m2.trimmed_member_id = memberId 
            UNION 
            SELECT m3.id, m3.trimmed_member_id as member_id, m3.reminder_type_id, m3.title, m3.reminder_time, DATE_FORMAT(m3.reminder_date,"%d-%m-%Y") as reminder_date, repeat_type, DATE_FORMAT(m3.repeat_till_date,"%d-%m-%Y") as repeat_till_date, m3.repeat_days FROM trimmed_member_reminders m3 WHERE m3.repeat_type =2  AND CURRENT_TIMESTAMP() <= CONCAT(m3.repeat_till_date," ",m3.reminder_time) AND  m3.trimmed_member_id = memberId;
            
            END IF;  
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetReminderList');
    }

}
