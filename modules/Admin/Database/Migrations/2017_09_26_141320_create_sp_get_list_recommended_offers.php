<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetListRecommendedOffers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getListRecommendedOffers;
        CREATE PROCEDURE getListRecommendedOffers(IN memberId INT(10), In date TIMESTAMP, In perPage INT(10))
        BEGIN
            SELECT O.id, O.offer_title, O.offer_description, O.offer_image, O.offer_detail_page_url, R.updated_at
            FROM offers O
            INNER JOIN
            member_offers_recommendations R ON O.id=R.offer_id
            WHERE R.updated_at < date AND O.status=1 AND R.status=1 AND (R.member_id=memberId OR R.member_id = 0)
            ORDER BY R.updated_at DESC
            LIMIT perPage;
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getListRecommendedOffers');
    }

}
