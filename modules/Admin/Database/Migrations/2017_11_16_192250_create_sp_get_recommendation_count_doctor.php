<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetRecommendationCountDoctor extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getRecommendationCountDoctor;
        CREATE PROCEDURE getRecommendationCountDoctor(IN memberId INT)
        BEGIN
            SELECT COUNT(id) as recommendation_count
            FROM member_medical_review
            WHERE member_id=memberId;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getRecommendationCountDoctor');
    }

}
