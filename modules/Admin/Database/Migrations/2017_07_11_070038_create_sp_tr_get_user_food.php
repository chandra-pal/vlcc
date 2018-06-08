<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetUserFood extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetUserFoods;
        CREATE PROCEDURE trGetUserFoods(IN memberId INT(10))
        BEGIN
            SELECT id, trimmed_member_id as member_id, diet_item, measure,calories, serving_size, serving_unit
            FROM trimmed_member_foods
            WHERE trimmed_member_id = memberId
            ORDER BY id DESC;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetUserFoods');
    }

}
