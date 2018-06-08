<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpCreateMemberAppoinmentBooking extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS memberAppoinmentBooking;
        CREATE PROCEDURE memberAppoinmentBooking(IN memberId INT(10),IN ditecianId INT(10), IN packageId INT(10), IN serviceId INT(10), IN bookingDate DATE, IN timeSlot TIME, IN endTime TIME, IN bookOla TINYINT(1), IN notificationText TEXT)
        BEGIN
            DECLARE sessionId INT;
 
            INSERT INTO member_session_bookings
            (member_id,dietician_id,package_id,service_id,session_date,start_time,end_time,ola_cab_required,status,created_at)
            VALUES
            (memberId, ditecianId, packageId, serviceId, bookingDate, timeSlot, endTime, bookOla, "1", CURRENT_TIMESTAMP());
            
            SELECT LAST_INSERT_ID() INTO sessionId;

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
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS memberAppoinmentBooking');
    }
}
