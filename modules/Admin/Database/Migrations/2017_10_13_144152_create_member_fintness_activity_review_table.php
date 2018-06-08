<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberFintnessActivityReviewTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_fintness_activity_review', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->unsigned();
            $table->string('static_posture_score', 50);
            $table->string('sit_and_reach_test', 50);
            $table->string('right_shoulder_flexibility', 50);
            $table->string('left_shoulder_flexibility', 50);
            $table->integer('pulse')->unsigned();
            $table->string('slr', 50);
            $table->string('specific_activity_advice', 50);
            $table->integer('specific_activity_duration');
            $table->string('physiotherapist_name', 100);
            $table->text('precautions_and_contraindications');
            $table->date('review_date');
            $table->foreign('member_id')->references('id')->on('members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_fintness_activity_review', function($table) {
            $table->dropForeign('member_fintness_activity_review_member_id_foreign');
            $table->dropIndex('member_fintness_activity_review_member_id_foreign');
        });
        Schema::drop('member_fintness_activity_review');
    }

}
