<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetMemberDetails extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS checkTrMemberExists;
        CREATE PROCEDURE checkTrMemberExists(IN mobileNumber varchar(20))
        BEGIN
            DECLARE affected_rows VARCHAR(5);
            IF EXISTS(SELECT id FROM trimmed_members WHERE mobile_number=mobileNumber COLLATE utf8_unicode_ci) THEN 
                SELECT 1 AS affected_rows;
            ELSE 
                SELECT 0 AS affected_rows;
            END IF;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS checkTrMemberExists');
    }

}
