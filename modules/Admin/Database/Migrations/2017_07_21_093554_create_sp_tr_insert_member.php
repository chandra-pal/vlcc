<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrInsertMember extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trInsertMember;
        CREATE PROCEDURE trInsertMember(IN first_name VARCHAR(50), IN last_name VARCHAR(50), IN email VARCHAR(100), IN mobile_number VARCHAR(20), IN app_version VARCHAR(30), IN platform TINYINT)
        BEGIN
            INSERT INTO trimmed_members
            (first_name,last_name,email,mobile_number,app_version,registered_from,
            trimmed_diet_plan_id,status,created_by,created_at)
            VALUES
            (first_name,last_name,email,mobile_number,app_version,platform,"1","1","1",CURRENT_TIMESTAMP());
            SELECT LAST_INSERT_ID() AS trimmed_member_id;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS trInsertMember');
    }

}
