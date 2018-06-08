<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpDrGetMemberBcaData extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getMemberBcaDataDoctor;
        CREATE PROCEDURE getMemberBcaDataDoctor(IN memberId INT)
        BEGIN
            SELECT bca.package_id, bca.member_id, bca.body_mass_index, 
            bca.basal_metabolic_rate, bca.fat_weight, bca.fat_percent, bca.lean_body_mass_weight, bca.lean_body_mass_percent,   
            bca.water_weight,  bca.water_percent, bca.visceral_fat_level, bca.visceral_fat_area, bca.target_weight,    
	    bca.target_fat_percent, IFNULL(packages.weight,0) as current_weight, bca.protein, bca.mineral, DATE_FORMAT(bca.recorded_date,"%d-%m-%Y") as bca_date
            FROM member_bca_details bca     
            LEFT OUTER JOIN
            member_packages packages ON 
            bca.package_id = packages.id
            WHERE bca.member_id = memberId 
            ORDER BY bca.id DESC
            LIMIT 0,1;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getMemberBcaDataDoctor');
    }

}
