<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetCurrentWeight extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCurrentWeight;
        CREATE PROCEDURE getCurrentWeight(IN memberId INT)
        BEGIN

            SELECT after_weight
            FROM member_session_record
            WHERE member_id=memberId
            AND DATE(recorded_date) >= (SELECT DATE(MAX(recorded_date)) FROM member_session_record WHERE member_id = memberId);

        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCurrentWeight');
    }

}
