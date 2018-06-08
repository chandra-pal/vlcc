<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterMemberActivityRecommendationTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_activity_recommendation', function(Blueprint $table) {
            $table->renameColumn('recommendation_date_time', 'recommendation_date');
            $table->integer('duration')->unsigned()->after('recommendation_text');
            $table->integer('calories_recommended')->unsigned()->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

    }

}
