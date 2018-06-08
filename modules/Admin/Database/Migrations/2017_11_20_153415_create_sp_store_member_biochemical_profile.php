<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpStoreMemberBiochemicalProfile extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS storeMemberBiochemicalProfile;
        CREATE PROCEDURE storeMemberBiochemicalProfile(IN memberId INT, IN testId INT, IN initailVal VARCHAR(100), IN finalVal VARCHAR(100))
        BEGIN
            INSERT INTO member_biochemical_profile (member_id,biochemical_condition_test_id,initial,final,created_at)
            VALUES (memberId,testId,initailVal,finalVal,CURRENT_TIMESTAMP());
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS storeMemberBiochemicalProfile');
    }

}
