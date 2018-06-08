<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrEditDietDiary extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trEditDietDiary;        
        CREATE PROCEDURE trEditDietDiary(IN dietLogId INT(10), IN servingsConsumed SMALLINT(5), IN totalCalories SMALLINT(5))     
        BEGIN 
            UPDATE trimmed_member_diet_logs
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
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trEditDietDiary');
    }

}
