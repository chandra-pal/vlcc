<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetNotifications extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getMemberNotifications;
        CREATE PROCEDURE getMemberNotifications(IN memberId INT, IN notificationId INT)
        BEGIN
            IF(notificationId > 0) THEN
                SELECT id, message_type, message_text,deep_link_screen,
                DATE_FORMAT(created_at, "%d-%m-%Y") as message_send_date,
                DATE_FORMAT(created_at, "%r") as message_send_time
                FROM member_notifications
                WHERE member_id = memberId AND status=1 AND id > notificationId
                AND created_at >= DATE_ADD(CURDATE(), INTERVAL -15 DAY)
                ORDER BY created_at DESC;
            ELSE
                SELECT id, message_type, message_text, deep_link_screen,
                DATE_FORMAT(created_at, "%d-%m-%Y") as message_send_date,
                DATE_FORMAT(created_at, "%r") as message_send_time
                FROM member_notifications
                WHERE member_id = memberId AND status=1 AND
                created_at >= DATE_ADD(CURDATE(), INTERVAL -15 DAY)
                ORDER BY created_at DESC;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getMemberNotifications');
    }

}
