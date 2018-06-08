<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberSkinHairAnalysisTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_skin_hair_analysis', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->string('skin_type', 20);
            $table->string('skin_condition', 20);
            $table->string('hyperpigmentation_type', 100);
            $table->string('hyperpigmentation_size', 100);
            $table->string('hyperpigmentation_depth', 100);
            $table->string('scars_depth', 100);
            $table->string('scars_size', 100);
            $table->tinyInteger('scars_pigmented')->comment = '0:no, 1:yes';
            $table->string('fine_lines_and_wrinkles', 100);
            $table->string('skin_curvature', 100);
            $table->string('other_marks', 20);
            $table->string('hair_type', 20);
            $table->string('condition_of_scalp', 20);
            $table->string('hair_density', 50);
            $table->string('condition_of_hair_shaft', 50);
            $table->text('history_of_allergy');
            $table->text('conclusion');
            $table->string('skin_and_hair_specialist_name', 50);
            $table->date('analysis_date')->nullable()->default(null);
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
        Schema::table('member_skin_hair_analysis', function($table) {
            $table->dropForeign('member_skin_hair_analysis_member_id_foreign');
            $table->dropIndex('member_skin_hair_analysis_member_id_foreign');
        });

        Schema::drop('member_skin_hair_analysis');
    }

}
