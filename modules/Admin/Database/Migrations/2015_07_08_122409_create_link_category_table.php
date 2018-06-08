<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkCategoryTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('link_categories', function(Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('category', 50)->unique()->index();
            $table->string('category_icon', 50);
            $table->string('header_text', 255);
            $table->integer('position')->unsigned();
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
        Schema::drop('link_categories');
    }

}
