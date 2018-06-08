<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetListRecommendationDate extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("DROP PROCEDURE IF EXISTS getListRecommendationWithDate;        
        CREATE PROCEDURE getListRecommendationWithDate(IN memberId INT, IN types INT, IN date DATE)     
        BEGIN 
            SELECT message_text, created_at
            FROM member_notifications
            WHERE member_id = memberId AND message_type = types AND status = 1 AND DATE(created_at) = date ORDER BY created_at DESC;     
        END       
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getListRecommendationWithDate');
    }

}
