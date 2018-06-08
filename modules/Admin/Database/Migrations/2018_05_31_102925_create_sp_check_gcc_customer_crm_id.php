<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpCheckGccCustomerCrmId extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS CheckGccCustomerCrmId;
        CREATE PROCEDURE CheckGccCustomerCrmId(IN mobileNumber varchar(30))
        BEGIN
            SELECT id,crm_customer_id,crm_center_id,first_name,last_name,email,dietician_username
            FROM members WHERE status=1 AND SUBSTRING(mobile_number, -8) = mobileNumber COLLATE utf8_unicode_ci LIMIT 1;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS CheckGccCustomerCrmId');
    }

}
