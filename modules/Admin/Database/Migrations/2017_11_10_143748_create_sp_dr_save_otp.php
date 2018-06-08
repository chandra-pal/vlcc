<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpDrSaveOtp extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS saveDoctorOtp;
        CREATE PROCEDURE saveDoctorOtp(IN mobile_number VARCHAR(10),IN otp VARCHAR(4),IN sms_delivered TINYINT,IN error_message VARCHAR(200),IN otp_used TINYINT,IN platform_generated_for TINYINT, IN otp_generated_for INT, OUT otp_id INT)
        BEGIN
            INSERT INTO doctor_otp(mobile_number, otp, sms_delivered, error_message, otp_used, platform_generated_for, otp_generated_for, created_at)
            VALUES (mobile_number, otp, sms_delivered, error_message, otp_used, platform_generated_for, otp_generated_for, CURRENT_TIMESTAMP());
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
        DB::unprepared('DROP PROCEDURE IF EXISTS saveDoctorOtp');
    }

}
