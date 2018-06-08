<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminNotificationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('admin_notifications', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('admin_id')->unsigned();
            $table->text('notification_text');
            $table->text('deep_linking');
            $table->datetime('notification_date');
            $table->boolean('notification_type')->unsigned()->comment = "1 : Deviation, 2 : Session Booking";
            $table->boolean('read_status')->default(false)->unsigned()->comment = "1 : Read, 0 : Unread";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('admin_id')->references('id')->on('admins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('admin_notifications', function($table) {
            $table->dropForeign('admin_notifications_admin_id_foreign');
            $table->dropIndex('admin_notifications_admin_id_foreign');
            $table->dropColumn('admin_id');
           
        });
        Schema::drop('admin_notifications');
    }

}
