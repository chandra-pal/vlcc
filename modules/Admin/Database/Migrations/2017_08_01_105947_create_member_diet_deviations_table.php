<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberDietDeviationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_diet_deviations', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->integer('diet_schedule_type_id')->unsigned();
            $table->smallinteger('calories_recommended')->unsigned();
            $table->smallinteger('calories_consumed')->unsigned();
            $table->date('deviation_date')->index();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreign('diet_schedule_type_id')->references('id')->on('diet_schedule_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_diet_deviations', function($table) {

            $table->dropForeign('member_diet_deviations_member_id_foreign');
            $table->dropIndex('member_diet_deviations_member_id_foreign');

            $table->dropForeign('member_diet_deviations_diet_schedule_type_id_foreign');
            $table->dropIndex('member_diet_deviations_diet_schedule_type_id_foreign');
        });
        Schema::drop('member_diet_deviations');
    }
}
