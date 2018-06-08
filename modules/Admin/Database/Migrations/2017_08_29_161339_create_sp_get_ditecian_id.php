<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetDitecianId extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDitecianId;
        CREATE PROCEDURE getDitecianId(IN memberId INT(10))
        BEGIN
            SELECT A.id 
            FROM members M
            INNER JOIN admins A ON 
            M.dietician_username = A.username
            WHERE M.id = memberId;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getDitecianId');
    }
}
