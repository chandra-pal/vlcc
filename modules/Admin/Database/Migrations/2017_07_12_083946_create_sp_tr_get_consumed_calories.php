<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetConsumedCalories extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetConsumedCalories;
        CREATE PROCEDURE trGetConsumedCalories(IN memberId INT(10))
        BEGIN
            SELECT 
            IFNULL(SUM(total_calories),0) as calories_consumed
            FROM trimmed_member_diet_logs
            WHERE trimmed_member_id = memberId
            AND diet_date = CURDATE();
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetConsumedCalories');
    }

}
