<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetImage extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getBeforeAfterImage;
        CREATE PROCEDURE getBeforeAfterImage(IN mobileNumber VARCHAR(20))
        BEGIN 
            SELECT MP.id, MP.before_image, MP.after_image 
            FROM member_package_images MP
            INNER JOIN 
            members M
            ON MP.member_id = M.id
            WHERE M.mobile_number = mobileNumber COLLATE utf8_unicode_ci;     
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getBeforeAfterImage');
    }

}
