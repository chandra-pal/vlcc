<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetRecommendationListDoctor extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getRecommendationListDoctor;
        CREATE PROCEDURE getRecommendationListDoctor(IN memberId INT, In pageNo INT(10), In perPage INT(10))
        BEGIN
            SELECT id, member_id, date, advice, created_by
            FROM member_medical_review
            WHERE member_id=memberId
            ORDER BY updated_at DESC
            LIMIT pageNo,perPage;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getRecommendationListDoctor');
    }

}
