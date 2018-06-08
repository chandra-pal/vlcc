<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetUserActivityRecommendation extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS userActivityRecommendation;
        CREATE PROCEDURE userActivityRecommendation(IN memberId INT)
        BEGIN         
            SELECT message_text,   
            DATE_FORMAT(created_at, "%d-%m-%Y") as message_send_time   
            FROM member_notifications
            WHERE member_id=memberId  AND status = 1 AND message_type = 2
            ORDER BY id DESC
            LIMIT 1;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS userActivityRecommendation');
    }

}
