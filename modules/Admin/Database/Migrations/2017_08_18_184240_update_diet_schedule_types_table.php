<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDietScheduleTypesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('diet_schedule_types', function(Blueprint $table) {
            if (!Schema::hasColumn('diet_schedule_types', 'start_time')) {
                $table->time('start_time')->after('schedule_name');
            }
            if (!Schema::hasColumn('diet_schedule_types', 'end_time')) {
                $table->time('end_time')->after('start_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('diet_schedule_types', function(Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }

}
