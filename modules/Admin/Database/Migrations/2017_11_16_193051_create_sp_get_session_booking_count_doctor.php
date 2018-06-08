<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetSessionBookingCountDoctor extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getSessionBookingCountDoctor;
        CREATE PROCEDURE getSessionBookingCountDoctor(IN memberId INT, IN sessionFlag INT(10))
        BEGIN
            if(sessionFlag = 0) THEN

                SELECT count(id) AS session_count
                FROM member_session_bookings
                WHERE member_id = memberId AND status=2 AND session_date>=CURRENT_DATE();

            ELSE

                SELECT count(id) AS session_count
                FROM member_session_bookings
                WHERE member_id = memberId AND status=5 AND session_date<CURRENT_DATE();

            END IF;


        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getSessionBookingCountDoctor');
    }

}
