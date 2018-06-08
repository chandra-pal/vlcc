<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetMaxCalories extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetMaxCalories;
        CREATE PROCEDURE trGetMaxCalories()
        BEGIN 
            SELECT config_value 
            FROM config_settings
            WHERE config_constant = "APP_CALORIES_LIMIT";     
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetMaxCalories');
    }

}
