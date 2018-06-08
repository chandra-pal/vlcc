<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberDietLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_diet_logs', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->string('food_name', 50)->index();
            $table->smallinteger('servings_consumed')->unsigned();
            $table->integer('diet_schedule_type_id')->unsigned();
            $table->string('measure', 50);
            $table->integer('calories')->unsigned();
            $table->integer('total_calories')->unsigned();
            $table->smallinteger('serving_size')->unsigned();
            $table->string('serving_unit', 20);
            $table->date('diet_date')->index();
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('diet_schedule_type_id')->references('id')->on('diet_schedule_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_diet_logs', function($table) {
            $table->dropForeign('member_diet_logs_member_id_foreign');
            $table->dropIndex('member_diet_logs_member_id_foreign');
            $table->dropColumn('member_id');

            $table->dropForeign('member_diet_logs_diet_schedule_type_id_foreign');
            $table->dropIndex('member_diet_logs_diet_schedule_type_id_foreign');
            $table->dropColumn('diet_schedule_type_id');
        });
        Schema::drop('member_diet_logs');
    }

}
