<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberDietaryAssessmentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_dietary_assessment', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->string('food_allergy', 256);
            $table->boolean('smoking')->unsigned()->comment = "0: No, 1: yes";
            $table->string('smoking_frequency', 50);
            $table->integer('meals_per_day')->unsigned();
            $table->integer('food_habbit')->unsigned();
            $table->smallinteger('eat_out_per_week')->unsigned();
            $table->boolean('alcohol')->unsigned()->comment = "0: No, 1: yes";
            $table->boolean('alcohol_frequency')->unsigned()->comment = "1:Veg, 2:Non Veg, 3:Ovo Veg, 4:Lacto Veg";
            $table->smallinteger('diet_total_calories')->unsigned();
            $table->smallinteger('diet_cho')->unsigned();
            $table->smallinteger('diet_protein')->unsigned();
            $table->smallinteger('diet_fat')->unsigned();
            $table->text('remark');
            $table->string('wellness_counsellor_name', 50);
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
        Schema::table('member_dietary_assessment', function($table) {
            $table->dropForeign('member_dietary_assessment_member_id_foreign');
            $table->dropIndex('member_dietary_assessment_member_id_foreign');
        });
        Schema::drop('member_dietary_assessment');
    }

}
