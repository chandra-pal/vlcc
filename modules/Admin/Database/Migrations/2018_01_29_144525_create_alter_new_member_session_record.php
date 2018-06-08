<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterNewMemberSessionRecord extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_session_record', function(Blueprint $table) {
            $table->boolean('otp_verified')->after('diet_and_activity_deviation')->unsigned()->comment = "1 : Yes, 0 : No";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
    }
}
