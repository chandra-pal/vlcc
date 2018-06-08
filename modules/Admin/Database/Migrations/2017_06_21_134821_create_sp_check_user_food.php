<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpCheckUserFood extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS checkUserFoods;        
        CREATE PROCEDURE checkUserFoods(IN memberId INT,IN foodName VARCHAR(50), IN foodTypeId INT)     
        BEGIN 
            SELECT id FROM foods
            WHERE food_type_id = foodTypeId
            AND UPPER(food_name) = UPPER(foodName) COLLATE utf8_unicode_ci;     
        END       
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS checkUserFoods');
    }

}
