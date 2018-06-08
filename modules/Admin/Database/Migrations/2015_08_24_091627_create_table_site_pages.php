<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSitePages extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('site_pages', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('page_name', 255)->unique()->index();
            $table->string('page_url', 255)->unique();
            $table->string('slug', 100)->unique()->index();
            $table->text('page_desc');
            $table->string('browser_title', 100);
            $table->string('meta_keywords', 255);
            $table->string('meta_description', 255);
            $table->text('page_content');
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
        Schema::drop('site_pages');
    }

}
