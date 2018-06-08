<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetLatestDeviceActivityForUser extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getLatestDeviceActivityForUser;
        CREATE PROCEDURE getLatestDeviceActivityForUser(IN memberId INT(10))
         BEGIN            
            SELECT member_id, max(created_at) AS latest_date
            FROM member_activity_logs
            WHERE activity_source = 2 AND member_id = memberId
            GROUP BY member_id;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getLatestDeviceActivityForUser');
    }

}
