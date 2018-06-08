<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetMemberNotificationsCount extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetMemberNotificationsCount;
        CREATE PROCEDURE trGetMemberNotificationsCount(IN memberId INT, IN notificationId INT)
        BEGIN 
            IF(notificationId > 0) THEN
                SELECT IFNULL(COUNT(id),0) AS notification_count
                FROM trimmed_member_notifications
                WHERE trimmed_member_id=memberId AND id > notificationId AND created_at >= DATE_ADD(CURDATE(), INTERVAL -15 DAY); 
            ELSE 
                SELECT IFNULL(COUNT(id),0) AS notification_count
                FROM trimmed_member_notifications
                WHERE trimmed_member_id=memberId AND created_at >= DATE_ADD(CURDATE(), INTERVAL -15 DAY); 
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
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetMemberNotificationsCount');
    }

}
