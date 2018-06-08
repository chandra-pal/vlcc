<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetClientCount extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getClientCount;
            CREATE PROCEDURE getClientCount(IN crmCenterId VARCHAR(255))
            BEGIN
               SELECT COUNT(id) as customer_count
               FROM members
               WHERE crm_center_id=crmCenterId COLLATE utf8_unicode_ci;
            END'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getClientCount');
    }

}
