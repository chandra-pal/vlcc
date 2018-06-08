<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpAddRecommendationDoctor extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS addRecommendationDoctor;
            CREATE PROCEDURE addRecommendationDoctor(IN doctorId INT, IN memberId INT, IN date DATE, IN recommendationId INT, IN recommendation TEXT)
            BEGIN
                IF(recommendationId IS NULL OR recommendationId = 0) THEN

                    INSERT into member_medical_review (member_id, date, advice, created_by, created_at, updated_at)
                    VALUES (memberId, CURDATE(), recommendation, doctorId, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP());

                    SELECT LAST_INSERT_ID() AS recommendation_id;

                ELSE

                    UPDATE member_medical_review
                    SET advice=recommendation, date=date, updated_at=CURRENT_TIMESTAMP()
                    WHERE created_by=doctorId
                    AND id=recommendationId;

                END IF;

            END'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS addRecommendationDoctor');
    }

}
