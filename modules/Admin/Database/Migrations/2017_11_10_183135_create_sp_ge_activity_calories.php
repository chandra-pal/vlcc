<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGeActivityCalories extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getActivityCalories;
        CREATE PROCEDURE getActivityCalories(IN activityType INT(10))
        BEGIN
            SELECT calories from activity_types
            WHERE id=activityType;

        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getActivityCalories');
    }

}
