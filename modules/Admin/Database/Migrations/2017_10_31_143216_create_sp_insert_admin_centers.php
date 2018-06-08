<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpInsertAdminCenters extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("DROP PROCEDURE IF EXISTS insertAdminCenters;
                CREATE PROCEDURE insertAdminCenters(IN adminId INT,IN crmCenterId VARCHAR(255))
                BEGIN
                    DECLARE centerId,status,lastInsertId INTEGER(10) DEFAULT 0;
                    SET FOREIGN_KEY_CHECKS=0;
                    SELECT id INTO centerId FROM vlcc_centers WHERE crm_center_id = crmCenterId COLLATE utf8_unicode_ci;
                    IF centerId != 0 THEN
                        IF NOT EXISTS (SELECT * FROM admin_centers WHERE user_id = adminId AND center_id = centerId) THEN
                            INSERT INTO admin_centers (user_id,center_id) VALUES (adminId,centerId);
                            IF LAST_INSERT_ID() IS NOT NULL THEN
                               SET status = 1;
                               SET lastInsertId =  LAST_INSERT_ID();
                            END IF;
                        END IF;
                    END IF;
                    SELECT status,lastInsertId;
                    SET FOREIGN_KEY_CHECKS=1;
                END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared("DROP PROCEDURE IF EXISTS insertAdminCenters;");
    }

}
