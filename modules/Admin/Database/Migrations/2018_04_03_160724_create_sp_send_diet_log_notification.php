<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpSendDietLogNotification extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sendDietLogNotification;
            CREATE PROCEDURE sendDietLogNotification(IN memberId INT, IN dietDate DATE)
            BEGIN       
                DECLARE admins_id INT;    
                DECLARE affected_rows INT;    
                SELECT id INTO admins_id FROM admins WHERE username = (SELECT dietician_username FROM members WHERE id=memberId);                 SELECT admins_id;
                IF(admins_id IS NULL) THEN    
                    SELECT 0 as affected_rows; 
                ELSE
                    SELECT 1 as affected_rows;                    
                    INSERT INTO admin_notifications (admin_id, notification_text, deep_linking, notification_date, notification_type, read_status, created_by, updated_by, created_at)  VALUES  (admins_id, "Customer has added diet log", CONCAT("member-diet-log/",DATE_FORMAT(dietDate,"%Y%m%d"),"-",memberId), CURRENT_TIMESTAMP(), 3, 0, 1, 0, CURRENT_TIMESTAMP()); 
                END IF;           
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sendDietLogNotification');
    }
}
