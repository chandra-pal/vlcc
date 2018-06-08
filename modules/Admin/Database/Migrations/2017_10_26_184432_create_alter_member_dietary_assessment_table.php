<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterMemberDietaryAssessmentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_dietary_assessment', function(Blueprint $table) {
            $table->integer('fasting')->unsigned()->after('eat_out_per_week');
            $table->integer('created_by')->unsigned()->after('assessment_date');
            $table->integer('updated_by')->unsigned()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }

}
