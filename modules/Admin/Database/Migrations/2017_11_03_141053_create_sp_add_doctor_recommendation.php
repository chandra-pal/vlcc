<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpAddDoctorRecommendation extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS addDoctorRecommendation;        
        CREATE PROCEDURE addDoctorRecommendation(IN memberId INT, IN sessionId INT, IN recommendation TEXT)     
        BEGIN 
            UPDATE member_session_bookings
            SET doctor_comment=recommendation
            WHERE id=sessionId;     
        END       
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS addDoctorRecommendation');
    }
}
