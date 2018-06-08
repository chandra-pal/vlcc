<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrimmedMemberActivityLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('trimmed_member_activity_logs', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('trimmed_member_id')->unsigned();
            $table->integer('activity_type_id')->unsigned();
            $table->string('activity', 50)->index();
            $table->smallinteger('duration')->unsigned();
            $table->time('start_time')->index();
            $table->date('activity_date')->index();
            $table->smallinteger('activity_source')->unsigned();
            $table->foreign('trimmed_member_id')->references('id')->on('trimmed_members');
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
        Schema::table('trimmed_member_activity_logs', function($table) {
            $table->dropForeign('trimmed_member_activity_logs_trimmed_member_id_foreign');
            $table->dropIndex('trimmed_member_activity_logs_trimmed_member_id_foreign');
            $table->dropColumn('trimmed_member_id');

            $table->dropForeign('trimmed_member_activity_logs_activity_type_id_foreign');
            $table->dropIndex('trimmed_member_activity_logs_activity_type_id_foreign');
            $table->dropColumn('activity_type_id');
        });
        Schema::drop('trimmed_member_activity_logs');
    }

}
