<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpCancelSessionBooking extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS cancelSessionBooking;
        CREATE PROCEDURE cancelSessionBooking(IN memberId INT(10), IN sessionId INT(10),IN ditecianId INT(10), IN notificationText TEXT)
        BEGIN
            UPDATE member_session_bookings
            SET status = 4
            WHERE member_id = memberId AND id = sessionId;

            INSERT INTO admin_notifications
            (admin_id, notification_text, deep_linking, notification_date, notification_type, read_status, created_by, created_at)
            VALUES
            (ditecianId, notificationText, CONCAT("session-bookings/", sessionId), CURRENT_TIMESTAMP(), "2", "0", memberId, CURRENT_TIMESTAMP());
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS cancelSessionBooking');
    }

}
