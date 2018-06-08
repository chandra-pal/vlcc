<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetReminderCategoriesWithReminderCount extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetReminderCategoriesWithReminderCount; 
            CREATE PROCEDURE trGetReminderCategoriesWithReminderCount()
        BEGIN
            SELECT reminder_type.id, reminder_type.type_name, reminder_type.status,
            IFNULL(COUNT(reminder.id),0) as reminders_count
            FROM  reminder_types reminder_type
            LEFT OUTER JOIN 
            trimmed_member_reminders reminder ON
            reminder_type.id = reminder.reminder_type_id            
            WHERE reminder_type.status=1 
            GROUP BY reminder_type.id;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetReminderCategoriesWithReminderCount');
    }

}
