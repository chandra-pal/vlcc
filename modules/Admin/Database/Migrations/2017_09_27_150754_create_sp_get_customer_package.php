<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetCustomerPackage extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerPackage;
        CREATE PROCEDURE getCustomerPackage(IN memberId INT(10))
        BEGIN
            SELECT package_title,MAX(total_payment)
            FROM member_packages
            WHERE member_id = memberId;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerPackage');
    }

}
