<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberMedicalReviewTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_medical_review', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->unsigned();
            $table->date('date')->nullable()->default(null);
            $table->string('advice', 256);
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
        Schema::table('member_medical_review', function($table) {
            $table->dropForeign('member_medical_review_member_id_foreign');
            $table->dropIndex('member_medical_review_member_id_foreign');
        });
        Schema::drop('member_medical_review');
    }

}
