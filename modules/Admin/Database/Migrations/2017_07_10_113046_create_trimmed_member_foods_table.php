<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrimmedMemberFoodsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('trimmed_member_foods', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('trimmed_member_id')->unsigned();
            $table->string('diet_item', 50)->index();
            $table->string('measure', 50);
            $table->integer('calories')->unsigned();
            $table->integer('serving_size')->unsigned();
            $table->string('serving_unit', 20);
            $table->foreign('trimmed_member_id')->references('id')->on('trimmed_members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('trimmed_member_foods', function($table) {
            $table->dropForeign('trimmed_member_foods_trimmed_member_id_foreign');
            $table->dropIndex('trimmed_member_foods_trimmed_member_id_foreign');
            $table->dropColumn('trimmed_member_id');
        });
        Schema::drop('trimmed_member_foods');
    }

}
