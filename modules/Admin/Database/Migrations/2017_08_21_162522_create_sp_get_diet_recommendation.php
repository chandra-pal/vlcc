<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetDietRecommendation extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDietRecommendation;
        CREATE PROCEDURE getDietRecommendation(IN memberId INT)
        BEGIN
           SELECT F.id, F.food_name, F.measure, F.calories, F.serving_size, F.serving_unit, R.servings_recommended, R.diet_schedule_type_id
            FROM foods F
            INNER JOIN member_diet_recommendations R ON F.id = R.food_id
            WHERE R.member_id = memberId AND  DATE(R.created_at) = CURDATE();
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getDietRecommendation');
    }

}
