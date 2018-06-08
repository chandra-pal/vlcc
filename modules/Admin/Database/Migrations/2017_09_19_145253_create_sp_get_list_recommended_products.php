<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetListRecommendedProducts extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getListRecommendedProducts;
        CREATE PROCEDURE getListRecommendedProducts(IN memberId INT(10), In date TIMESTAMP, In perPage INT(10))
        BEGIN
            SELECT P.id, P.product_title, P.product_description, P.product_image, P.product_detail_page_url, R.updated_at
            FROM products P
            INNER JOIN
            member_product_recommendations R ON P.id=R.product_id
            WHERE R.updated_at < date AND P.status=1 AND R.status=1 AND (R.member_id=memberId OR R.member_id = 0)
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getListRecommendedProducts');
    }

}
