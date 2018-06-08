<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpCreateActivity extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS userActivityAdd;        
        CREATE PROCEDURE userActivityAdd(IN member_id INT, IN activity_type_id INT, IN date DATE, IN activity VARCHAR(50), IN duration SMALLINT(5), IN start_time TIME, IN caloriesBurn INT)     
        BEGIN           
            INSERT INTO member_activity_logs(
               member_id, activity_type_id, activity, duration, calories_burned, start_time, activity_date, activity_source, created_at
            )            
            VALUES (
                member_id, activity_type_id, activity, duration, caloriesBurn, start_time, date, 1, CURRENT_TIMESTAMP()
            );     
            SELECT LAST_INSERT_ID() AS activity_id;
        END       
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS userActivityAdd');
    }

}
