<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetContactDetails extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("DROP PROCEDURE IF EXISTS getContactDetails;        
        CREATE PROCEDURE getContactDetails(IN configValue INT)     
        BEGIN 
            SELECT config_value
            FROM config_settings
            WHERE config_category_id = configValue;     
        END       
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getContactDetails');
    }

}
