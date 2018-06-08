<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpCheckMemberAvailability extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS checkMemberAvailability;
        CREATE PROCEDURE checkMemberAvailability(IN crmCustomerId VARCHAR(20), IN mobileNumber VARCHAR(20), IN firstName VARCHAR(50),IN lastName VARCHAR(50),IN email VARCHAR(100),IN appVersion VARCHAR(30),IN registeredFrom TINYINT, IN crmCenterId VARCHAR(255))
       BEGIN
            DECLARE member_id INT;

            UPDATE members SET status=0 WHERE mobile_number=mobileNumber COLLATE utf8_unicode_ci AND crm_customer_id <> crmCustomerId COLLATE utf8_unicode_ci;

            IF EXISTS(SELECT id FROM members WHERE crm_customer_id=crmCustomerId  COLLATE utf8_unicode_ci AND mobile_number=mobileNumber COLLATE utf8_unicode_ci) THEN                
                SELECT id as member_id FROM members WHERE crm_customer_id=crmCustomerId COLLATE utf8_unicode_ci AND mobile_number=mobileNumber COLLATE utf8_unicode_ci;
                
            ELSE            
                INSERT INTO members(crm_customer_id, crm_center_id, first_name, last_name, email, mobile_number, app_version, registered_from, diet_plan_id,status,created_by,created_at)
                VALUES (crmCustomerId, crmCenterId, firstName, lastName, email, mobileNumber, appVersion, registeredFrom, 0, 1, 1, CURRENT_TIMESTAMP());
                IF LAST_INSERT_ID() > 0 THEN
                    SELECT LAST_INSERT_ID() AS member_id;
                END IF;
            END IF;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS checkMemberAvailability');
    }

}
