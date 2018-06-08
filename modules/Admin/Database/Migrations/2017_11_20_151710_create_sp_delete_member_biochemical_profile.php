<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpDeleteMemberBiochemicalProfile extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS deleteMemberBiochemicalProfile;
        CREATE PROCEDURE deleteMemberBiochemicalProfile(IN memberId INT)
        BEGIN
            DELETE FROM member_biochemical_profile WHERE member_id=memberId;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS deleteMemberBiochemicalProfile');
    }

}
