<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpCheckAppoinment extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS checkAppoinment;
        CREATE PROCEDURE checkAppoinment(IN memberId INT(10), IN serviceId INT(10), IN bookingDate DATE, IN ditecianId INT(10))
        BEGIN
           SELECT id
           FROM member_session_bookings
           WHERE member_id=memberId AND session_date = bookingDate AND service_id = serviceId 
           AND dietician_id = ditecianId AND (status = 1 OR status = 2);
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS checkAppoinment');
    }

}
