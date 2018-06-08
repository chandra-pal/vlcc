<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetActivityRecommendation extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getActivityRecommendation;
        CREATE PROCEDURE getActivityRecommendation(IN memberId INT(10))
        BEGIN
            SELECT R.id, R.activity_type_id, R.recommendation_date, R.duration, R.calories_recommended, A.activity_type
            FROM member_activity_recommendation R 
            INNER JOIN activity_types A
            ON R.activity_type_id = A.id
            WHERE R.member_id = memberId
            AND DATE(R.recommendation_date) <= CURDATE() 
            AND DATE(R.recommendation_date) >= (SELECT DATE(MAX(recommendation_date)) FROM member_activity_recommendation WHERE member_id = memberId 
            AND DATE(recommendation_date) <= CURDATE() );
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getActivityRecommendation');
    }

}
