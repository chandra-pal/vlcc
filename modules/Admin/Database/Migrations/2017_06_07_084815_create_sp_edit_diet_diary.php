<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpEditDietDiary extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS editDietDiary;        
        CREATE PROCEDURE editDietDiary(IN dietLogId INT(10), IN servingsConsumed SMALLINT(5), IN totalCalories SMALLINT(5))     
        BEGIN 
            UPDATE member_diet_logs
            SET servings_consumed=servingsConsumed,
            total_calories=totalCalories
            WHERE id=dietLogId;     
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
        DB::unprepared('DROP PROCEDURE IF EXISTS editDietDiary');
    }

}
