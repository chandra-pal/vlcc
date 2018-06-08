<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigSettingsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('config_settings', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('config_constant', 100)->unique()->index();
            $table->text('config_value');
            $table->string('description', 255);
            $table->smallInteger('config_category_id')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('config_category_id')->references('id')->on('config_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('config_settings');
    }

}
