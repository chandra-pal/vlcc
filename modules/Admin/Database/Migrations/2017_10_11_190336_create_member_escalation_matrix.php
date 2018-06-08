<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberEscalationMatrix extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_escalation_matrix', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('admin_id')->unsigned()->comment('Refers to ID of user from admins table, to whom the escalation has been made to.');
            $table->integer('session_id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->integer('package_id')->unsigned();
            $table->float('weight_loss');
            $table->float('weight_gain');
            $table->text('ath_comment')->nullable();
            $table->date('escalation_date');
            $table->tinyInteger('escalation_status');
            $table->foreign('admin_id')->references('id')->on('admins');
            $table->foreign('session_id')->references('id')->on('member_session_bookings');
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('package_id')->references('id')->on('member_packages');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_escalation_matrix', function($table) {
            $table->dropForeign('member_escalation_matrix_admin_id_foreign');
            $table->dropIndex('member_escalation_matrix_admin_id_foreign');
            $table->dropColumn('admin_id');
            
            $table->dropForeign('member_escalation_matrix_session_id_foreign');
            $table->dropIndex('member_escalation_matrix_session_id_foreign');
            $table->dropColumn('session_id');
            
            $table->dropForeign('member_escalation_matrix_member_id_foreign');
            $table->dropIndex('member_escalation_matrix_member_id_foreign');
            $table->dropColumn('member_id');
            
            $table->dropForeign('member_escalation_matrix_package_id_foreign');
            $table->dropIndex('member_escalation_matrix_package_id_foreign');
            $table->dropColumn('package_id');
        });
        Schema::drop('member_escalation_matrix');
    }

}
