<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetActivityDeviation extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getActivityDeviation;
        CREATE PROCEDURE getActivityDeviation(IN memberId INT(10))

        BEGIN
            SELECT
            (( select IFNULL(sum(calories_burned),0) from member_activity_logs
            where member_id = memberId AND DATE(activity_date) = CURDATE() ) -

            ( select IFNULL(sum(calories_recommended),0) from member_activity_recommendation
            where member_id = memberId AND DATE(recommendation_date) <= CURDATE()
            AND DATE(recommendation_date) >= (SELECT DATE(MAX(recommendation_date)) FROM member_activity_recommendation WHERE member_id = memberId
            AND DATE(recommendation_date) <= CURDATE() ))) as activity_deviation;

        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getActivityDeviation');
    }

}
