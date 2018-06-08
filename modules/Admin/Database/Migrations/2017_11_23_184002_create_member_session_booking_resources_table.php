<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberSessionBookingResourcesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_session_booking_resources', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('session_id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->integer('machine_id')->unsigned();
            $table->integer('room_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->time('machine_start_time');
            $table->time('machine_end_time');
            $table->time('room_start_time');
            $table->time('room_end_time');
            $table->time('staff_start_time');
            $table->time('staff_end_time');
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('session_id')->references('id')->on('member_session_bookings');
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('machine_id')->references('id')->on('machines');
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->foreign('staff_id')->references('id')->on('admins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_session_booking_resources', function($table) {
            $table->dropForeign('member_session_booking_resources_session_id_foreign');
            $table->dropIndex('member_session_booking_resources_session_id_foreign');
            $table->dropColumn('session_id');

            $table->dropForeign('member_session_booking_resources_member_id_foreign');
            $table->dropIndex('member_session_booking_resources_member_id_foreign');
            $table->dropColumn('member_id');
            
            $table->dropForeign('member_session_booking_resources_machine_id_foreign');
            $table->dropIndex('member_session_booking_resources_machine_id_foreign');
            $table->dropColumn('machine_id');
            
            $table->dropForeign('member_session_booking_resources_room_id_foreign');
            $table->dropIndex('member_session_booking_resources_room_id_foreign');
            $table->dropColumn('room_id');
            
            $table->dropForeign('member_session_booking_resources_staff_id_foreign');
            $table->dropIndex('member_session_booking_resources_staff_id_foreign');
            $table->dropColumn('staff_id');
        });
        Schema::drop('member_session_booking_resources');
    }
}
