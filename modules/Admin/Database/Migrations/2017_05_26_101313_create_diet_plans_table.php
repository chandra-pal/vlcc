<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDietPlansTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('diet_plans', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('plan_name', 20)->index();
            $table->boolean('plan_type')->unsigned()->comment = '1: Veg, 2: Non Veg';
            $table->smallinteger('calories')->unsigned()->index();
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('diet_plans');
    }

}
