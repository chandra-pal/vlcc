<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrimmedMemberNotificationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('trimmed_member_notifications', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('trimmed_member_id')->unsigned();
            $table->boolean('message_type')->unsigned()->comment = "1: Normal Notification, 2: Activity Recommendation, 3: Diet Recommendation";
            $table->string('message_text', 320);
            $table->dateTime('message_send_time')->index();
            $table->string('deep_like_screen', 50);
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->foreign('trimmed_member_id')->references('id')->on('trimmed_members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('trimmed_member_notifications', function($table) {
            $table->dropForeign('trimmed_member_notifications_trimmed_member_id_foreign');
            $table->dropIndex('trimmed_member_notifications_trimmed_member_id_foreign');
            $table->dropColumn('trimmed_member_id');
        });
        Schema::drop('trimmed_member_notifications');
    }

}
