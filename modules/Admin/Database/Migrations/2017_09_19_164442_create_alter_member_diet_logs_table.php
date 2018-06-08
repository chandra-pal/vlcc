<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterMemberDietLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_diet_logs', function(Blueprint $table) {
            $table->integer('food_id')->unsigned()->nullable()->after('member_id');
            $table->foreign('food_id')->references('id')->on('foods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_diet_logs', function($table) {
            $table->dropForeign('member_diet_logs_food_id_foreign');
            $table->dropIndex('member_diet_logs_food_id_foreign');
        });
    }

}
