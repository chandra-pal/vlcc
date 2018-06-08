<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrCheckUserFood extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trCheckUserFoods;        
        CREATE PROCEDURE trCheckUserFoods(IN memberId INT,IN dietItem VARCHAR(50))     
        BEGIN 
            SELECT id
            FROM trimmed_member_foods
            where trimmed_member_id = memberId AND UPPER(diet_item) = UPPER(dietItem) COLLATE utf8_unicode_ci;     
        END       
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trCheckUserFoods');
    }

}
