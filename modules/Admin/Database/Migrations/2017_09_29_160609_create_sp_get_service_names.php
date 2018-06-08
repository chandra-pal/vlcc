<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetServiceNames extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getServiceNames;
        CREATE PROCEDURE getServiceNames(IN serviceId TEXT)
        BEGIN
             SELECT id, service_name FROM member_package_services WHERE FIND_IN_SET(id, serviceId); 
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getServiceNames');
    }

}
