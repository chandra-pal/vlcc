<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrCheckMemberExists extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getTrMemberDetails;
        CREATE PROCEDURE getTrMemberDetails(IN mobileNumber varchar(20))
        BEGIN
            SELECT id, first_name, last_name, email FROM  trimmed_members
            WHERE mobile_number = mobileNumber COLLATE utf8_unicode_ci;      
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getTrMemberDetails');
    }
}
