<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberSessionRecordSummaryTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_session_record_summary', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->integer('session_id')->unsigned();
            $table->date('recorded_date');
            $table->decimal('net_weight_loss', 10, 2);
            $table->decimal('net_weight_gain', 10, 2);
            $table->decimal('balance_programme_kg', 10, 2);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('package_id')->references('id')->on('member_packages');
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('session_id')->references('id')->on('member_session_bookings');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_session_record_summary', function($table) {
            $table->dropForeign('member_session_record_summary_member_id_foreign');
            $table->dropIndex('member_session_record_summary_member_id_foreign');
            $table->dropColumn('member_id');

            $table->dropForeign('member_session_record_summary_package_id_foreign');
            $table->dropIndex('member_session_record_summary_package_id_foreign');
            $table->dropColumn('package_id');

            $table->dropForeign('member_session_record_summary_session_id_foreign');
            $table->dropIndex('member_session_record_summary_session_id_foreign');
            $table->dropColumn('session_id');
        });
        Schema::drop('member_session_record_summary');
    }

}
