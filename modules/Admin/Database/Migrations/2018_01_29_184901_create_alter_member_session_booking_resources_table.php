<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterMemberSessionBookingResourcesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_session_booking_resources', function(Blueprint $table) {
            $table->dropForeign('member_session_booking_resources_machine_id_foreign');
            $table->dropColumn('machine_id');

            $table->dropForeign('member_session_booking_resources_room_id_foreign');
            $table->dropColumn('room_id');

            $table->dropForeign('member_session_booking_resources_staff_id_foreign');
            $table->dropColumn('staff_id');

            $table->dropColumn('machine_start_time');
            $table->dropColumn('machine_end_time');
            $table->dropColumn('room_start_time');
            $table->dropColumn('room_end_time');
            $table->dropColumn('staff_start_time');
            $table->dropColumn('staff_end_time');
            $table->dropColumn('status');

            $table->integer('resource_id')->unsigned()->after('member_id');
            $table->boolean('resource_type')->unsigned()->after('resource_id')->comment = "1 : Staff, 2 : Machine, 3 : Room";

            $table->time('resource_start_time')->after('resource_type');
            $table->time('resource_end_time')->after('resource_start_time');
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
