<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrInsertRegistrationOtp extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS trInsertRegistrationOtp;
        CREATE PROCEDURE trInsertRegistrationOtp(IN mobileNumber VARCHAR(10),IN otp VARCHAR(4),IN smsDelivered TINYINT,IN errorMessage VARCHAR(200),IN otpUsed TINYINT,IN platformGeneratedFor TINYINT, IN otpGeneratedFor INT, OUT otpId INT)
        BEGIN
            INSERT INTO trimmed_member_otp(mobile_number, otp, sms_delivered, error_message, otp_used, platform_generated_for, otp_generated_for, created_at)
            VALUES (mobileNumber, otp, smsDelivered, errorMessage, otpUsed, platformGeneratedFor, otpGeneratedFor, CURRENT_TIMESTAMP());
            SELECT LAST_INSERT_ID() AS otp_id, CURRENT_TIMESTAMP() as otp_creation_time;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS trInsertRegistrationOtp');
    }

}
