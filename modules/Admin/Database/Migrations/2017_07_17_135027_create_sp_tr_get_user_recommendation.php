<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetUserRecommendation extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetUserRecommendation;
        CREATE PROCEDURE trGetUserRecommendation(IN memberId INT)
        BEGIN           
            SELECT message_text, created_at
            FROM trimmed_member_notifications
            WHERE trimmed_member_id = memberId AND status = 1 AND message_type = 3
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
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetUserRecommendation');
    }

}
