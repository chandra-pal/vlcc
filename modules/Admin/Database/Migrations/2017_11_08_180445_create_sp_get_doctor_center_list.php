<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetDoctorCenterList extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDoctorCenterList;
            CREATE PROCEDURE getDoctorCenterList(IN doctorId INT)
            BEGIN
               SELECT C.id,C.center_name,C.crm_center_id 
               FROM vlcc_centers C 
               INNER JOIN 
               admin_centers A 
               ON C.id=A.center_id 
               WHERE A.user_id=doctorId;
            END'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDoctorCenterList');
    }

}
