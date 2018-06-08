<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrCreateUserFood extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trCreateUserFood;
        CREATE PROCEDURE trCreateUserFood(IN member_id INT(10),IN diet_item VARCHAR(50),IN measure VARCHAR(50),IN calories INT(10),IN serving_size INT(10), IN serving_unit VARCHAR(20))
        BEGIN
            INSERT INTO trimmed_member_foods(trimmed_member_id, diet_item, measure, calories, serving_size, serving_unit, created_at)
            VALUES (member_id, diet_item, measure, calories, serving_size, serving_unit, CURRENT_TIMESTAMP());
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trCreateUserFood');
    }

}
