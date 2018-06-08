<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetLatestDeviceActivityForUser extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetLatestDeviceActivityForUser;
        CREATE PROCEDURE trGetLatestDeviceActivityForUser(IN memberId INT)
        BEGIN                        
            SELECT trimmed_member_id, max(created_at) AS latest_date            
            FROM trimmed_member_activity_logs            
            WHERE activity_source = 2 AND trimmed_member_id = memberId            
            GROUP BY trimmed_member_id;        
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetLatestDeviceActivityForUser');
    }

}
