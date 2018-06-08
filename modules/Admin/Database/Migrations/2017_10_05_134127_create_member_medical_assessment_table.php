<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberMedicalAssessmentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_medical_assessment', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->string('current_associated_medical_problem', 100);
            $table->date('epilepsy')->nullable()->default(null);
            $table->string('other', 100);
            $table->string('physical_finding', 100);
            $table->string('systemic_examination', 100);
            $table->string('gynae_obstetrics_history', 100);
            $table->string('clients_birth_weight', 100);
            $table->boolean('sleeping_pattern')->unsigned()->comment = "1: Normal, 2: Disturbed, 3:Less Sleep";
            $table->string('past_mediacl_history', 100);
            $table->string('family_history_of_diabetes_obesity', 100);
            $table->string('detailed_history', 100);
            $table->string('treatment_history', 100);
            $table->string('suggested_investigation', 100);
            $table->date('followup_date')->nullable()->default(null);
            $table->string('doctors_name', 100);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
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
        Schema::table('member_medical_assessment', function($table) {
            $table->dropForeign('member_medical_assessment_member_id_foreign');
            $table->dropIndex('member_medical_assessment_member_id_foreign');
        });
        Schema::drop('member_medical_assessment');
    }

}
