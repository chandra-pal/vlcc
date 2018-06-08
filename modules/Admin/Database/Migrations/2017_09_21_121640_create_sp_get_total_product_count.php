<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetTotalProductCount extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getTotalProductCount;
        CREATE PROCEDURE getTotalProductCount(IN memberId INT(10))
        BEGIN
            SELECT COUNT(id) as recommended_product_count
            FROM member_product_recommendations
            WHERE (member_id=memberId OR member_id = 0) AND status=1;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getTotalProductCount');
    }

}
