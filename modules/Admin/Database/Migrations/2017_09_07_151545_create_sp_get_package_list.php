<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetPackageList extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getPackageList;
        CREATE PROCEDURE getPackageList(IN memberId INT(10))
        BEGIN
           SELECT id,package_title
           FROM member_packages
           WHERE member_id=memberId;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getPackageList');
    }

}
