<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinksTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('links', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('link_name', 50)->unique()->index();
            $table->string('link_icon', 50);
            $table->string('link_url', 100)->unique()->index();
            $table->smallInteger('link_category_id')->unsigned();
            $table->integer('position')->unsigned();
            $table->string('page_header', 100)->unique()->index();
            $table->text('page_text')->nullable();
            $table->smallInteger('pagination')->unsigned();
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
        Schema::drop('links');
    }

}
