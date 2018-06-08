<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterMemberSessionTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_session_bookings', function (Blueprint $table) {
            Schema::rename('member_sessions', 'member_session_bookings');
            $table->boolean('status')->default(true)->unsigned()->comment("1: Requested, 2: Booked, 3: Rejected (by dietitian), 4: Canceled (by dietitian), 5: Canceled (by customer), 6: In Progress, 7: Completed, 8: Not attended")->after('attendance_status')->change();
            $table->integer('package_id')->unsigned()->after('physiotherpist_id');
            $table->dropForeign('member_sessions_doctor_id_foreign');
            $table->dropIndex('member_sessions_doctor_id_foreign');

            $table->dropForeign('member_sessions_physiotherpist_id_foreign');
            $table->dropIndex('member_sessions_physiotherpist_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_session_bookings', function (Blueprint $table) {
            Schema::rename('member_session_bookings', 'member_sessions');
        });
    }

}
