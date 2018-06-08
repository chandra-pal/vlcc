<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterOfferCountSp extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getTotalOfferCount;
        CREATE PROCEDURE getTotalOfferCount(IN memberId INT(10))
        BEGIN
            SELECT COUNT(id) as recommended_offer_count
            FROM member_offers_recommendations
            WHERE (member_id = memberId OR member_id=0) AND status=1;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getTotalOfferCount');
    }

}
