<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpDrGetSessionList extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getSessionBookingListDoctor;

        CREATE PROCEDURE getSessionBookingListDoctor(IN memberId INT, In pageNo INT(10), In perPage INT(10), IN sessionFlag INT(10))
        BEGIN
            IF(sessionFlag = 0) THEN

                SELECT S.id, S.package_id, S.service_id, S.session_date, S.start_time, S.ola_cab_required, S.status, S.doctor_comment, R.bp,R.before_weight,R.after_weight
                FROM member_session_bookings S
                LEFT JOIN member_session_record R
                ON S.id=R.session_id
                WHERE S.member_id = memberId AND S.status=2 AND S.session_date>=CURRENT_DATE()
                ORDER BY S.session_date DESC
                LIMIT pageNo,perPage;

            ELSE

                SELECT S.id, S.package_id, S.service_id, S.session_date, S.start_time, S.ola_cab_required, S.status, S.doctor_comment, R.bp,R.before_weight,R.after_weight
                FROM member_session_bookings S
                LEFT JOIN member_session_record R
                ON S.id=R.session_id
                WHERE S.member_id = memberId AND S.status=5 AND S.session_date<=CURRENT_DATE()
                ORDER BY S.session_date DESC
                LIMIT pageNo,perPage;

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
        DB::unprepared('DROP PROCEDURE IF EXISTS getSessionBookingList');
    }

}
