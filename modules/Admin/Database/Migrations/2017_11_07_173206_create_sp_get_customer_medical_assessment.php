<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetCustomerMedicalAssessment extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerMedicalAssessment;
        CREATE PROCEDURE getCustomerMedicalAssessment(IN memberId INT)
        BEGIN
            SELECT * from member_medical_assessment where member_id=memberId
            LIMIT 1;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getCustomerMedicalAssessment');
    }

}
