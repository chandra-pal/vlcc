<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpCheckMemberBiochemicalProfile extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS checkMemberBiochemicalProfile;
        CREATE PROCEDURE checkMemberBiochemicalProfile(IN memberId INT)
        BEGIN
            SELECT id FROM member_biochemical_profile WHERE member_id=memberId;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS checkMemberBiochemicalProfile');
    }

}
