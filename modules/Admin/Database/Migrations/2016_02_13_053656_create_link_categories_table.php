<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkCategoriesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('link_categories', function($table) {
            //$table->smallInteger('menu_group_id')->after('id')->unsigned();
            $table->smallInteger('menu_group_id')->after('id')->unsigned();
            $table->foreign('menu_group_id')->references('id')->on('menu_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('link_categories', function($table) {
            $table->dropForeign('link_categories_menu_group_id_foreign');
            $table->dropIndex('link_categories_menu_group_id_foreign');
            $table->dropColumn('menu_group_id');
        });
    }

}
