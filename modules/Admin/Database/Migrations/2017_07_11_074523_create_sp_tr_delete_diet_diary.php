<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrDeleteDietDiary extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trDeteDietDiary;
        CREATE PROCEDURE trDeteDietDiary(IN dietLogId INT(10))
        BEGIN
            DELETE FROM trimmed_member_diet_logs WHERE id=dietLogId; 
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trDeteDietDiary');
    }

}
