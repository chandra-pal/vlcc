<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrDeleteReminder extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trDeleteReminder;
        CREATE PROCEDURE trDeleteReminder(IN reminderId INT)
        BEGIN
            DELETE FROM trimmed_member_reminders WHERE id = reminderId;  
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trDeleteReminder');
    }

}
