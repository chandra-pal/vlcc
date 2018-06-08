<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberActivityRecommendationTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_activity_recommendation', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('activity_type_id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->timestamp('recommendation_date_time')->nullable()->default(null);
            $table->string('recommendation_text', 255);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('activity_type_id')->references('id')->on('activity_types');
            $table->foreign('member_id')->references('id')->on('members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_activity_recommendation', function($table) {
            $table->dropForeign('member_activity_recommendation_activity_type_id_foreign');
            $table->dropIndex('member_activity_recommendation_activity_type_id_foreign');
            $table->dropColumn('activity_type_id');

            $table->dropForeign('member_activity_recommendation_member_id_foreign');
            $table->dropIndex('member_activity_recommendation_member_id_foreign');
            $table->dropColumn('member_id');
        });
        Schema::drop('member_activity_recommendation');
    }

}
