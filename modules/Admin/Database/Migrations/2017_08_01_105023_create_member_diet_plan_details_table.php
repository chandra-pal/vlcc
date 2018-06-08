<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberDietPlanDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_diet_plan_details', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->integer('diet_plan_id')->unsigned();
            $table->integer('diet_schedule_type_id')->unsigned();            
            $table->integer('food_id')->unsigned();            
            $table->smallinteger('servings_recommended')->unsigned();            
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreign('diet_plan_id')->references('id')->on('diet_plans')->onDelete('cascade');
            $table->foreign('diet_schedule_type_id')->references('id')->on('diet_schedule_types')->onDelete('cascade');           
            $table->foreign('food_id')->references('id')->on('foods')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_diet_plan_details', function($table) {
            
            $table->dropForeign('member_diet_plan_details_member_id_foreign');
            $table->dropIndex('member_diet_plan_details_member_id_foreign');
            $table->dropColumn('member_id');
            
            $table->dropForeign('member_diet_plan_details_diet_plan_id_foreign');
            $table->dropIndex('member_diet_plan_details_diet_plan_id_foreign');
            $table->dropColumn('diet_plan_id');
            
            $table->dropForeign('member_diet_plan_details_diet_schedule_type_id_foreign');
            $table->dropIndex('member_diet_plan_details_diet_schedule_type_id_foreign');
            $table->dropColumn('diet_schedule_type_id');            

            $table->dropForeign('member_diet_plan_details_food_id_foreign');
            $table->dropIndex('member_diet_plan_details_food_id_foreign');
            $table->dropColumn('food_id');
        });
        Schema::drop('member_diet_plan_details');
    }

}
