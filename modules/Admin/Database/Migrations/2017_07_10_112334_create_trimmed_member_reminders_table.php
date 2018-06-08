<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrimmedMemberRemindersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('trimmed_member_reminders', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('trimmed_member_id')->unsigned();
            $table->smallinteger('reminder_type_id')->unsigned();
            $table->string('title', 50)->index();
            $table->time('reminder_time');
            $table->date('reminder_date');
            $table->boolean('repeat_type')->unsigned()->comment = "0: Never, 1: Lifetime, 2: Until Date";
            $table->date('repeat_till_date')->nullable()->default(null);
            $table->string('repeat_days', 50);
            $table->foreign('trimmed_member_id')->references('id')->on('trimmed_members');
            $table->foreign('reminder_type_id')->references('id')->on('reminder_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('trimmed_member_reminders', function($table) {
            $table->dropForeign('trimmed_member_reminders_reminder_type_id_foreign');
            $table->dropIndex('trimmed_member_reminders_reminder_type_id_foreign');
            $table->dropColumn('reminder_type_id');

            $table->dropForeign('trimmed_member_reminders_trimmed_member_id_foreign');
            $table->dropIndex('trimmed_member_reminders_trimmed_member_id_foreign');
            $table->dropColumn('trimmed_member_id');
        });
        Schema::drop('trimmed_member_reminders');
    }

}
