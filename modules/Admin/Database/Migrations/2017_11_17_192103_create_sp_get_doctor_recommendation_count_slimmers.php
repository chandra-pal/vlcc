<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetDoctorRecommendationCountSlimmers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDoctorRecommendationCountSlimmer;
        CREATE PROCEDURE getDoctorRecommendationCountSlimmer(IN memberId INT(10))
        BEGIN
            SELECT COUNT(id) as comment_count
            FROM member_medical_review
            WHERE member_id = memberId;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDoctorRecommendationCountSlimmer');
    }

}
