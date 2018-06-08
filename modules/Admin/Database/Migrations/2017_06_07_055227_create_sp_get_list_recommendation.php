<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetListRecommendation extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getListRecommendation;
        CREATE PROCEDURE getListRecommendation(IN memberId INT(10), IN types TINYINT(1))
        BEGIN 
            SELECT message_text, created_at
            FROM member_notifications
            WHERE member_id = memberId AND message_type = types AND status = 1 ORDER BY created_at DESC;     
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getListRecommendation');
    }

}
