<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpDeviationService extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS deviationService;
            CREATE PROCEDURE deviationService()
            deviationService: BEGIN
            DECLARE memberId,dietScheduleTypeId, totalServingsRecommended,totalCaloriesRecomanded,totalCaloriesConsumed,notFound INTEGER DEFAULT 0;

            DECLARE dietIndividualCursor CURSOR FOR
                SELECT mdpd.member_id,mdpd.diet_schedule_type_id,IFNULL(SUM(mdpd.servings_recommended),0),IFNULL(SUM((f.calories * mdpd.servings_recommended)),0)
                FROM member_diet_plan_details AS mdpd
                JOIN foods AS f ON mdpd.food_id = f.id
                WHERE FIND_IN_SET(mdpd.diet_schedule_type_id, (SELECT GROUP_CONCAT(id SEPARATOR ',')  FROM diet_schedule_types WHERE TIME_FORMAT(end_time,'%H:%i') <= TIME_FORMAT(CURTIME(),'%H:%i')))
                GROUP BY mdpd.member_id,mdpd.diet_schedule_type_id;

            DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = 1;

            OPEN dietIndividualCursor;
                getDeviationScheduleWise: LOOP

                    FETCH dietIndividualCursor INTO memberId,dietScheduleTypeId,totalServingsRecommended,totalCaloriesRecomanded;

                    IF notFound = 1 THEN
                        LEAVE getDeviationScheduleWise;
                    END IF;

                    IF EXISTS (SELECT * FROM member_diet_deviations WHERE member_id = memberId AND diet_schedule_type_id = dietScheduleTypeId AND deviation_date = CURDATE()) THEN
                        SELECT IFNULL(SUM(total_calories),0) INTO totalCaloriesConsumed
                        FROM member_diet_logs
                        WHERE member_id = memberId
                        AND diet_date = CURDATE()
                        AND diet_schedule_type_id = dietScheduleTypeId
                        HAVING SUM(total_calories) != totalCaloriesRecomanded;

                        IF totalCaloriesConsumed = totalCaloriesRecomanded THEN
                            DELETE FROM member_diet_deviations WHERE member_id = memberId AND diet_schedule_type_id = dietScheduleTypeId AND deviation_date = CURDATE();
                        ELSE
                            UPDATE member_diet_deviations SET calories_recommended = totalCaloriesRecomanded,
                            calories_consumed = totalCaloriesConsumed,
                            updated_by = 1, updated_at = CURRENT_TIMESTAMP()
                            WHERE member_id = memberId
                            AND diet_schedule_type_id = dietScheduleTypeId
                            AND deviation_date = CURDATE();
                        END IF;

                    ELSE
                        INSERT INTO member_diet_deviations (member_id, diet_schedule_type_id, calories_recommended, calories_consumed, deviation_date, created_by, updated_by, created_at)
                        SELECT member_id, diet_schedule_type_id, totalCaloriesRecomanded,IFNULL(SUM(total_calories),0), diet_date,1,1,CURRENT_TIMESTAMP()
                        FROM member_diet_logs
                        WHERE member_id = memberId
                        AND diet_schedule_type_id = dietScheduleTypeId
                        AND diet_date = CURDATE()
                        HAVING SUM(total_calories) != totalCaloriesRecomanded;
                    END IF;

                END LOOP getDeviationScheduleWise;
            CLOSE dietIndividualCursor;


            inner_block:BEGIN

                DECLARE adminId,done INTEGER(10) DEFAULT 0;
                DECLARE messageText, deepLink TEXT DEFAULT '';

                DECLARE adminNotificationCursor CURSOR FOR

                    SELECT a.id, CONCAT('Deviation for ',COUNT(mdd.id),' Customers in ',dst.schedule_name,' Schedule'),
                    CONCAT('member-diet-deviation/',DATE_FORMAT(mdd.deviation_date,'%Y%m%d'),'-',mdd.diet_schedule_type_id)
                    FROM member_diet_deviations AS mdd
                    JOIN members AS m ON mdd.member_id = m.id
                    JOIN admins AS a ON m.dietician_username = a.username
                    JOIN diet_schedule_types AS dst ON mdd.diet_schedule_type_id = dst.id
                    WHERE diet_schedule_type_id = dietScheduleTypeId
                    AND deviation_date = CURDATE()
                    GROUP BY m.dietician_username;

                    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

                    OPEN adminNotificationCursor;
                    adminNotificationCursorLoop: LOOP

                        FETCH adminNotificationCursor INTO adminId,messageText,deepLink;

                        IF done = 1 THEN
                            LEAVE adminNotificationCursorLoop;
                        END IF;

                        IF NOT EXISTS(SELECT * FROM  admin_notifications WHERE admin_id=adminId AND deep_linking LIKE deepLink COLLATE utf8_unicode_ci AND DATE_FORMAT(notification_date,'%y-%m-%d')=CURDATE()) THEN

                            INSERT INTO admin_notifications (admin_id, notification_text, deep_linking, notification_date, notification_type, read_status, created_by, updated_by, created_at)
                        VALUES (adminId,messageText,deepLink,CURRENT_TIMESTAMP(),1, 0, 1, 0, CURRENT_TIMESTAMP());

                        END IF;

                END LOOP adminNotificationCursorLoop;
                CLOSE adminNotificationCursor;

            END;

            SELECT dietScheduleTypeId,'SUCCESS' AS response;

        END;"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS deviationService');
    }

}
