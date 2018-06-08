<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetVlccContactNo extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getContactNo;
        CREATE PROCEDURE getContactNo()
        BEGIN 
            SELECT config_value 
            FROM config_settings
            WHERE config_constant = "APP_CONTACT_PHONE";     
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getContactNo');
    }

}
