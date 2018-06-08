<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiochemicalConditionTestTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('biochemical_condition_test', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('condition_id')->unsigned();
            $table->string('test_name', 100);
            $table->foreign('condition_id')->references('id')->on('biochemical_condition');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('biochemical_condition_test', function($table) {
            $table->dropForeign('biochemical_condition_test_condition_id_foreign');
            $table->dropIndex('biochemical_condition_test_condition_id_foreign');
        });
        Schema::drop('biochemical_condition_test');
    }

}
