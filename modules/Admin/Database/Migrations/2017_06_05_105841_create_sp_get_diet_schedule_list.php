<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetDietScheduleList extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDietScheduleList;        
        CREATE PROCEDURE getDietScheduleList()     
        BEGIN           
            SELECT id, schedule_name
            FROM diet_schedule_types
            WHERE status = 1;     
        END       
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDietScheduleList');
    }

}
