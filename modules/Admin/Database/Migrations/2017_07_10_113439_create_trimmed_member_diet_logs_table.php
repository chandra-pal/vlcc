<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrimmedMemberDietLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('trimmed_member_diet_logs', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('trimmed_member_id')->unsigned();
            $table->string('food_name', 50)->index();
            $table->smallinteger('servings_consumed')->unsigned();
            $table->integer('diet_schedule_type_id')->unsigned();
            $table->string('measure', 50);
            $table->integer('calories')->unsigned();
            $table->integer('total_calories')->unsigned();
            $table->smallinteger('serving_size')->unsigned();
            $table->string('serving_unit', 20);
            $table->date('diet_date')->index();
            $table->foreign('trimmed_member_id')->references('id')->on('trimmed_members');
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
        Schema::table('trimmed_member_diet_logs', function($table) {
            $table->dropForeign('trimmed_member_diet_logs_trimmed_member_id_foreign');
            $table->dropIndex('trimmed_member_diet_logs_trimmed_member_id_foreign');
            $table->dropColumn('trimmed_member_id');

            $table->dropForeign('trimmed_member_diet_logs_diet_schedule_type_id_foreign');
            $table->dropIndex('trimmed_member_diet_logs_diet_schedule_type_id_foreign');
            $table->dropColumn('diet_schedule_type_id');
        });
        Schema::drop('trimmed_member_diet_logs');
    }

}
