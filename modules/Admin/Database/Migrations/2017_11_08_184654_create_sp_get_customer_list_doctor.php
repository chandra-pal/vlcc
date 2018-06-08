<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetCustomerListDoctor extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerListDoctor;
            CREATE PROCEDURE getCustomerListDoctor(IN crmCenterId VARCHAR(255), In pageNo INT(10), In perPage INT(10) ,IN searchFilter VARCHAR(100))
            BEGIN

                IF(searchFilter IS NULL OR searchFilter = "") THEN

                    SELECT M.id, M.first_name, M.last_name, M.created_at, P.before_image
                    FROM members M
                    LEFT JOIN
                    member_package_images P
                    ON M.id=P.member_id
                    WHERE M.crm_center_id=crmCenterId COLLATE utf8_unicode_ci
                    ORDER BY M.first_name ASC
                    LIMIT pageNo,perPage;

                ELSE

                    SELECT M.id, M.first_name, M.last_name, M.created_at, P.before_image
                    FROM members M
                    LEFT JOIN
                    member_package_images P
                    ON M.id=P.member_id
                    WHERE M.crm_center_id=crmCenterId COLLATE utf8_unicode_ci
                    HAVING CONCAT(M.first_name," ",M.last_name) LIKE CONCAT("%",searchFilter COLLATE utf8_unicode_ci,"%")
                    ORDER BY M.first_name ASC
                    LIMIT pageNo,perPage;

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
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerListDoctor');
    }

}
