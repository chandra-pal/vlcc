<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberLoginLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_login_logs', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->boolean('login_through')->unsigned()->comment = "1: Android, 2: iOS";
            $table->time('login_time')->index();
            $table->string('ip_address', 45)->index();
            $table->boolean('is_login')->unsigned();
            $table->string('session_id', 255);
            $table->foreign('member_id')->references('id')->on('members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_login_logs', function($table) {
            $table->dropForeign('member_login_logs_member_id_foreign');
            $table->dropIndex('member_login_logs_member_id_foreign');
            $table->dropColumn('member_id');
        });
        Schema::drop('member_login_logs');
    }

}
