<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetMeasurements extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getMeasurements;
        CREATE PROCEDURE getMeasurements()
        BEGIN
            SELECT id, title, meaning, status
            FROM measurements
            WHERE status=1 
            ORDER BY id DESC;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getMeasurements');
    }
}
