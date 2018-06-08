<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetDoctorCenterCustomerCount extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerCount;
            CREATE PROCEDURE getCustomerCount(IN crmCenterId VARCHAR(255), IN searchFilter VARCHAR(100))
            BEGIN

                IF(searchFilter IS NULL OR searchFilter = "") THEN

                    SELECT COUNT(id) AS customerCount
                    FROM members
                    WHERE crm_center_id=crmCenterId COLLATE utf8_unicode_ci;

                ELSE

                    SELECT COUNT(id) AS customerCount
                    FROM members
                    WHERE crm_center_id=crmCenterId COLLATE utf8_unicode_ci
                    AND CONCAT(first_name," ",last_name) LIKE CONCAT("%",searchFilter COLLATE utf8_unicode_ci,"%");

                END IF;

            END'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerCount');
    }

}
