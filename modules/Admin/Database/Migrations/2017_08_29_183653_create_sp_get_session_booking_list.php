<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetSessionBookingList extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getSessionBookingList;
        CREATE PROCEDURE getSessionBookingList(IN memberId INT)
        BEGIN
            SELECT S.id, S.package_id, S.service_id, S.session_date, S.start_time, S.ola_cab_required, S.status, S.doctor_comment
            FROM member_session_bookings S
            WHERE S.member_id = memberId
            ORDER BY S.id DESC;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getSessionBookingList');
    }

}
