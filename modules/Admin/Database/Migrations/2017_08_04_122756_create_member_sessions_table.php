<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberSessionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_sessions', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->integer('dietician_id')->unsigned();
            $table->integer('doctor_id')->unsigned();
            $table->integer('physiotherpist_id')->unsigned();
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');            
            $table->text('dietitian_comment');
            $table->text('doctor_comment');
            $table->text('physiotherpist_comment');
            $table->boolean('ola_cab_required')->default(false)->unsigned()->comment = "1 : Yes, 0 : No";
            $table->boolean('attendance_status')->default(true)->unsigned()->comment = "1 : Yes, 0 : No";
            $table->text('cancellation_comment'); 
            $table->boolean('status')->default(true)->unsigned()->comment = "1: booked, 2: in progress, 3: completed, 4: cancelled by dietitian, 5: cancelled by member, 6: not happened";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();            
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('dietician_id')->references('id')->on('admins');
            $table->foreign('doctor_id')->references('id')->on('admins');
            $table->foreign('physiotherpist_id')->references('id')->on('admins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_sessions', function($table) {
//            $table->dropForeign('member_sessions_member_id_foreign');
//            $table->dropIndex('member_sessions_member_id_foreign');
//            
//            $table->dropForeign('member_sessions_dietician_id_foreign');
//            $table->dropIndex('member_sessions_dietician_id_foreign');
        });
        Schema::drop('member_sessions');
    }

}
