<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetUserLatestRecommendation extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getUserRecommendation;        
        CREATE PROCEDURE getUserRecommendation(IN memberId INT)     
        BEGIN           
            SELECT message_text, created_at
            FROM member_notifications
            WHERE member_id = memberId AND status = 1 AND message_type = 3
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getUserRecommendation');
    }

}
