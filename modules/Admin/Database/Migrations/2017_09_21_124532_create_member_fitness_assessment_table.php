<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberFitnessAssessmentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_fitness_assessment', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->string('static_posture', 50);
            $table->string('sit_and_reach_test', 50);
            $table->string('shoulder_flexibility_right', 30);
            $table->string('shoulder_flexibility_left', 30);
            $table->integer('pulse')->unsigned();
            $table->boolean('back_problem_test')->unsigned()->comment = "0: No, 1: yes";
            $table->string('current_activity_type', 100);
            $table->string('current_activity_frequency', 100);
            $table->string('current_activity_duration', 100);
            $table->text('remark');
            $table->boolean('home_care_kit')->unsigned()->comment = "0: No, 1: yes";
            $table->string('physiotherapist_name', 50);
            $table->date('assessment_date');
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
        Schema::table('member_fitness_assessment', function($table) {
            $table->dropForeign('member_fitness_assessment_member_id_foreign');
            $table->dropIndex('member_fitness_assessment_member_id_foreign');
        });
        Schema::drop('member_fitness_assessment');
    }

}
