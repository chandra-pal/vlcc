<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrUserActivityRecommendation extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trUserActivityRecommendation;
        CREATE PROCEDURE trUserActivityRecommendation(IN memberId INT)
        BEGIN         
            SELECT message_text,   
            DATE_FORMAT(created_at, "%d-%m-%Y") as message_send_time   
            FROM trimmed_member_notifications
            WHERE trimmed_member_id=memberId     
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
        DB::unprepared('DROP PROCEDURE IF EXISTS trUserActivityRecommendation');
    }

}
