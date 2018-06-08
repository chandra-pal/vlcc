<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpDeleteActivity extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS userActivityDelete;        
        CREATE PROCEDURE userActivityDelete(IN memberId INT)     
        BEGIN           
            DELETE FROM member_activity_logs 
            WHERE id=memberId;     
        END       
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS userActivityDelete');
    }

}
