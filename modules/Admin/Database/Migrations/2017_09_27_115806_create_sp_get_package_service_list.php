<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetPackageServiceList extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getPackageServiceList;
        CREATE PROCEDURE getPackageServiceList(IN memberId INT(10), IN packageId INT(10))
        BEGIN
           SELECT id, service_name, DATE_FORMAT(end_date,"%d-%m-%Y") as service_validity
           FROM member_package_services
           WHERE member_id=memberId AND package_id=packageId;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getPackageServiceList');
    }

}
