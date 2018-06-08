<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetPackageDetails extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getPackageDetails;
        CREATE PROCEDURE getPackageDetails(IN memberId INT)
        BEGIN

            SELECT start_date,end_date,weight
            FROM member_packages
            WHERE member_id=memberId
            AND DATE(updated_at) >= (SELECT DATE(MAX(updated_at)) FROM member_packages WHERE member_id = memberId);

        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getPackageDetails');
    }

}
