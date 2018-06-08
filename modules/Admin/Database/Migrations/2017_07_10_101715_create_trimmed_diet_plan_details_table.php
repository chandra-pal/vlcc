<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrimmedDietPlanDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trimmed_diet_plan_details', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('food_id')->unsigned();
            $table->integer('trimmed_diet_plan_id')->unsigned();
            $table->integer('diet_schedule_type_id')->unsigned();
            $table->smallinteger('servings_recommended')->unsigned();
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('diet_schedule_type_id')->references('id')->on('diet_schedule_types')->onDelete('cascade');
            $table->foreign('trimmed_diet_plan_id')->references('id')->on('trimmed_diet_plans')->onDelete('cascade');
            $table->foreign('food_id')->references('id')->on('foods')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trimmed_diet_plan_details', function($table) {
            $table->dropForeign('trimmed_diet_plan_details_diet_schedule_type_id_foreign');
            $table->dropIndex('trimmed_diet_plan_details_diet_schedule_type_id_foreign');
            $table->dropColumn('diet_schedule_type_id');

            $table->dropForeign('trimmed_diet_plan_details_trimmed_diet_plan_id_foreign');
            $table->dropIndex('trimmed_diet_plan_details_trimmed_diet_plan_id_foreign');
            $table->dropColumn('trimmed_diet_plan_id');

            $table->dropForeign('trimmed_diet_plan_details_food_id_foreign');
            $table->dropIndex('trimmed_diet_plan_details_food_id_foreign');
            $table->dropColumn('food_id');
        });
        Schema::drop('trimmed_diet_plan_details');
    }

}
