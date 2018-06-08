<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetUserFoodCount extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getUserFoodCount;
        CREATE PROCEDURE getUserFoodCount(IN memberId INT(10),In date TIMESTAMP, IN searchFilter VARCHAR(100))
        BEGIN

            IF(searchFilter IS NULL OR searchFilter = "") THEN

                SELECT COUNT(food.id) as user_food_count
                FROM foods food
                INNER JOIN
                food_types food_type ON
                food.food_type_id = food_type.id
                WHERE food.created_by_user_type = 0 AND food.created_by = memberId;

            ELSE

                SELECT COUNT(food.id) as user_food_count
                FROM foods food
                INNER JOIN
                food_types food_type ON
                food.food_type_id = food_type.id
                WHERE food.created_by_user_type = 0 AND food.created_by = memberId AND food.food_name LIKE CONCAT(searchFilter COLLATE utf8_unicode_ci,"%");

            END IF;

        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getUserFoodCount');
    }

}
