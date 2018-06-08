<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpCreateUserFood extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS createUserFood;
        CREATE PROCEDURE createUserFood(IN food_type_id INT(10), IN member_id INT(10),IN food_name VARCHAR(50),IN measure VARCHAR(50),IN calories INT(10), OUT food_id INT)
        BEGIN
            INSERT INTO  foods(food_type_id, food_name, measure, calories, created_by_user_type, created_by, created_at)
            VALUES (food_type_id, food_name, measure, calories, 0, member_id, CURRENT_TIMESTAMP());
            SELECT LAST_INSERT_ID() AS food_id;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS createUserFood');
    }

}
