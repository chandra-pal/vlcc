<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberActivityDeviationTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_activity_deviation', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->integer('calories_recommended')->unsigned();
            $table->integer('calories_burned')->unsigned();
            $table->date('deviation_date')->index();
            $table->foreign('member_id')->references('id')->on('members');
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_activity_deviation', function($table) {
            $table->dropForeign('member_activity_deviation_member_id_foreign');
            $table->dropIndex('member_activity_deviation_member_id_foreign');
            $table->dropColumn('member_id');
        });
        Schema::drop('member_activity_deviation');
    }

}
