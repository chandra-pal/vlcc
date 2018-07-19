<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterMemberSessionBookingsAddCrmCenterId extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_session_bookings', function(Blueprint $table) {
            $table->string('crm_center_id', 255)->after('package_id')->comment="Center where session was booked.";
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
