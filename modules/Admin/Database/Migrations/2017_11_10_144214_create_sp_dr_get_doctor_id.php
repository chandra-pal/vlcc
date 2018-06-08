<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpDrGetDoctorId extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDoctorId;
        CREATE PROCEDURE getDoctorId(IN mobileNumber VARCHAR(10), OUT otpId INT)
        BEGIN
            SELECT admin.id as doctor_id, user_type.name FROM admins admin
            INNER JOIN user_types user_type ON
            admin.user_type_id = user_type.id            
            WHERE admin.contact = mobileNumber COLLATE utf8_unicode_ci;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getDoctorId');
    }

}
