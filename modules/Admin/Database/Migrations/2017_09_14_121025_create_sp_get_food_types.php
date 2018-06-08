<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetFoodTypes extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getFoodTypeList;
        CREATE PROCEDURE getFoodTypeList()
        BEGIN
            SELECT id, food_type_name
            FROM food_types
            WHERE status = 1 AND id <> 1
            ORDER BY food_type_name ASC;            
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getFoodTypeList');
    }

}
