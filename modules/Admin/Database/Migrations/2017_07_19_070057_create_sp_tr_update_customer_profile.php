<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrUpdateCustomerProfile extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trUpdateCustomerProfile;
         CREATE PROCEDURE trUpdateCustomerProfile(IN memberId INT, IN firstName VARCHAR(50), IN lastName VARCHAR(50), IN email VARCHAR(100), IN dob DATE, IN height SMALLINT, IN weight SMALLINT, IN gender TINYINT)
         BEGIN         
             UPDATE trimmed_members
             SET 
             first_name = firstName, last_name = lastName, email = email, dob = dob, weight = weight, height = height, gender = gender
             WHERE id = memberId;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trUpdateCustomerProfile');
    }

}
