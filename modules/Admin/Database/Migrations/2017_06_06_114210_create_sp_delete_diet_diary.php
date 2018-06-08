<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpDeleteDietDiary extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS deleteDietDiary;
        CREATE PROCEDURE deleteDietDiary(IN dietLogId INT(10))
        BEGIN
            DELETE FROM member_diet_logs WHERE id=dietLogId; 
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
        DB::unprepared('DROP PROCEDURE IF EXISTS deleteDietDiary');
    }

}
