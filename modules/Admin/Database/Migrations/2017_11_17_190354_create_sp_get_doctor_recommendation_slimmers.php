<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetDoctorRecommendationSlimmers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDoctorRecommendationSlimmers;
        CREATE PROCEDURE getDoctorRecommendationSlimmers(IN memberId INT(10), In date TIMESTAMP, In perPage INT(10), IN recommendationId INT(10))
        BEGIN

            IF(recommendationId = "" OR recommendationId = 0) THEN

                SELECT advice, updated_at
                FROM member_medical_review
                WHERE updated_at < date AND member_id=memberId
                ORDER BY updated_at DESC
                LIMIT perPage;

            ELSE

                SELECT advice, updated_at
                FROM member_medical_review
                WHERE id=recommendationId;

            END IF;

        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDoctorRecommendationSlimmers');
    }

}
