<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetConfigConstantByNameTemp extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getConfigConstantsByName;
        CREATE PROCEDURE getConfigConstantsByName(IN configConstantsString TEXT)
         BEGIN            
            SELECT config_constant, config_value 
            FROM config_settings 
            WHERE FIND_IN_SET(config_constant COLLATE utf8_unicode_ci,configConstantsString COLLATE utf8_unicode_ci);        
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getConfigConstantsByName');
    }

}
