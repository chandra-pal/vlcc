<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetCustomerDetails extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetCustomerDetails;
        CREATE PROCEDURE trGetCustomerDetails(IN mobileNumber varchar(20))
        BEGIN
            SELECT first_name, last_name, email, dob, height, weight, gender
            FROM trimmed_members
            WHERE mobile_number=mobileNumber COLLATE utf8_unicode_ci;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetCustomerDetails');
    }

}
