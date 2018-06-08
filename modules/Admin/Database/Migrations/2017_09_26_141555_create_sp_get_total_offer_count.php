<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetTotalOfferCount extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getTotalOfferCount;
        CREATE PROCEDURE getTotalOfferCount(IN memberId INT(10))
        BEGIN
            SELECT IFNULL(COUNT(O.id),0) AS recommended_offer_count
            FROM offers O
            INNER JOIN
            member_offers_recommendations R ON O.id=R.offer_id
            WHERE O.status=1 AND R.status=1 AND (R.member_id=memberId OR R.member_id = 0);
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
