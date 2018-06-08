<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberActivityLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_activity_logs', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->integer('activity_type_id')->unsigned();
            $table->string('activity', 50)->index();
            $table->smallinteger('duration')->unsigned();
            $table->time('start_time')->index();
            $table->date('activity_date')->index();
            $table->boolean('activity_source')->default(true)->unsigned()->comment = "1 : Manual, 2 : Device";
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('activity_type_id')->references('id')->on('activity_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_activity_logs', function($table) {
            $table->dropForeign('member_activity_logs_member_id_foreign');
            $table->dropIndex('member_activity_logs_member_id_foreign');
            $table->dropColumn('member_id');
            
            $table->dropForeign('member_activity_logs_activity_type_id_foreign');
            $table->dropIndex('member_activity_logs_activity_type_id_foreign');
            $table->dropColumn('activity_type_id');
        });
        Schema::drop('member_activity_logs');
    }

}
