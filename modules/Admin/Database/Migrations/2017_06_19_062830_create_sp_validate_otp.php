<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpValidateOtp extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS validateOtp;
        CREATE PROCEDURE validateOtp(IN mobileNumber VARCHAR(10),
        IN otpNumber VARCHAR(4), IN otpId INT)
        BEGIN
            DECLARE affected_rows VARCHAR(5);
            IF EXISTS (SELECT id FROM member_otp WHERE mobile_number=mobileNumber COLLATE utf8_unicode_ci 
            AND otp = otpNumber COLLATE utf8_unicode_ci 
            AND id = otpId) THEN 
            	UPDATE member_otp SET otp_used=1, attempt_count = attempt_count + 1 WHERE id=otpId;
                IF(ROW_COUNT() >0) THEN 
                 SELECT 1 AS affected_rows;
                ELSE 
                  SELECT 0 AS affected_rows;
                END IF;  
            ELSE     
            	SELECT 2 AS affected_rows;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS validateOtp');
    }

}
