<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoodsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('foods', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('food_name', 50)->unique()->index();
            $table->string('measure',50)->index();
            $table->integer('calories')->unsigned();
            $table->integer('serving_size')->unsigned();
            $table->string('serving_unit', 20);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('foods');
    }
}
