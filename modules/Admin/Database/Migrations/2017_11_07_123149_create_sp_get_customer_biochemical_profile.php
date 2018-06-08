<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetCustomerBiochemicalProfile extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerBiochemicalProfile;
        CREATE PROCEDURE getCustomerBiochemicalProfile(IN memberId INT)
        BEGIN
            SELECT * from member_biochemical_profile
            where member_id=memberId;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerBiochemicalProfile');
    }

}
