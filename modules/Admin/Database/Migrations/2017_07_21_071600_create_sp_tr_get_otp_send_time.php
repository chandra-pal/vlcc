<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetOtpSendTime extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetOtpSendTime;
        CREATE PROCEDURE trGetOtpSendTime(IN clientOtp VARCHAR(4), IN otpId INT)
        BEGIN
            SELECT created_at
            FROM trimmed_member_otp
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
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetOtpSendTime');
    }

}
