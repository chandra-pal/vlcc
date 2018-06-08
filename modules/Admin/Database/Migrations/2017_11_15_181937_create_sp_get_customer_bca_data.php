<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetCustomerBcaData extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerBcaData;
        CREATE PROCEDURE getCustomerBcaData(IN memberId varchar(20))
        BEGIN
            SELECT *
            FROM member_bca_details
            WHERE member_id=memberId
            AND DATE(recorded_date) >= (SELECT DATE(MAX(recorded_date)) FROM member_bca_details WHERE member_id = memberId);
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerBcaData');
    }

}
