<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpCreateCheckMemberDeviceTokenAvailability extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS checkDeviceTokenAvailability;
        CREATE PROCEDURE checkDeviceTokenAvailability(IN memberId INT(10), IN deviceToken VARCHAR(255), IN deviceType TINYINT(1))
        BEGIN   
            IF EXISTS(SELECT id FROM member_device_tokens WHERE member_id = memberId) THEN     
                UPDATE member_device_tokens SET device_token = deviceToken, device_type = deviceType where member_id = memberId;
                IF(ROW_COUNT() >0) THEN 
                    SELECT 1 AS affected_rows;
                ELSE 
                    SELECT 0 AS affected_rows;
                END IF; 
            ELSE 
                INSERT INTO member_device_tokens(member_id, device_token, device_type, status, created_at)            
                VALUES (memberId, deviceToken, deviceType, 1, CURRENT_TIMESTAMP());
                IF LAST_INSERT_ID() > 0 THEN
                    SELECT 1 AS affected_rows; 
                ELSE 
                    SELECT 0 AS affected_rows;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS checkDeviceTokenAvailability');
    }

}
