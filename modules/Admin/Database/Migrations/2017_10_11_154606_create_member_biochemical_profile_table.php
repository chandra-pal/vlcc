<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberBiochemicalProfileTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_biochemical_profile', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->unsigned();
            $table->integer('biochemical_condition_test_id')->unsigned();
            $table->string('initial', 100);
            $table->string('final', 100);
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('biochemical_condition_test_id')->references('id')->on('biochemical_condition_test');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_biochemical_profile', function($table) {
            $table->dropForeign('member_biochemical_profile_member_id_foreign');
            $table->dropIndex('member_biochemical_profile_member_id_foreign');

            $table->dropForeign('member_biochemical_profile_biochemical_condition_test_id_foreign');
            $table->dropIndex('member_biochemical_profile_biochemical_condition_test_id_foreign');
        });
        Schema::drop('member_biochemical_profile');
    }

}
