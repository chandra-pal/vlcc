<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetOtpSendTime extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getOtpSendTime;
        CREATE PROCEDURE getOtpSendTime(IN clientOtp VARCHAR(4), IN otpId INT)
        BEGIN
            SELECT created_at
            FROM member_otp
            WHERE id = otpId AND otp = clientOtp COLLATE utf8_unicode_ci;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getOtpSendTime');
    }

}
