<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrCreateDietDiary extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trCreateDietDiary;
        CREATE PROCEDURE trCreateDietDiary(IN memberId INT(10),IN foodName VARCHAR(50),IN servingsConsumed SMALLINT(5), IN dietScheduleTypeId INT(10), IN measure VARCHAR(50),IN calories INT(10),IN totalCalories INT(10),IN servingSize SMALLINT(5), IN servingUnit VARCHAR(20),IN dietDate DATE)
        BEGIN	
	      DECLARE diet_log_id INT;            
          SELECT id into diet_log_id FROM trimmed_member_diet_logs WHERE trimmed_member_id=memberId AND diet_date=dietDate AND diet_schedule_type_id=dietScheduleTypeId AND food_name=foodName COLLATE utf8_unicode_ci;
          IF(FOUND_ROWS() > 0) THEN 
           	UPDATE trimmed_member_diet_logs SET servings_consumed=servings_consumed+servingsConsumed, total_calories=total_calories+totalCalories WHERE id=diet_log_id; 
            IF(ROW_COUNT() >0) THEN 
                 SELECT "0" AS diet_log_id;
            ELSE 
            	 SELECT "1" AS diet_log_id;
            END IF;  
	    ELSE 
             INSERT INTO trimmed_member_diet_logs(trimmed_member_id, food_name, servings_consumed, diet_schedule_type_id, measure, calories, total_calories, serving_size, serving_unit, diet_date, created_at)
            VALUES (memberId, foodName, servingsConsumed, dietScheduleTypeId, measure, calories, totalCalories, servingSize, servingUnit, dietDate, CURRENT_TIMESTAMP());
            IF LAST_INSERT_ID() > 0 THEN
             SELECT LAST_INSERT_ID() AS diet_log_id;     
            END IF; 
	    END IF;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trCreateDietDiary');
    }

}
