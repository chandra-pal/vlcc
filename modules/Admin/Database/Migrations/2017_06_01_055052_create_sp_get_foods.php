<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetFoods extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared('DROP PROCEDURE IF EXISTS getFoods;
        CREATE PROCEDURE `getFoods`(IN `date` TIMESTAMP, IN `perPage` INT(10), IN `searchFilter` VARCHAR(100))
        BEGIN
            IF(searchFilter IS NULL OR searchFilter = "") THEN

                SELECT food.id, food.created_by as member_id, food.food_type_id, food_type.food_type_name, food.food_name, food.measure, food.calories, food.serving_size, food.serving_unit, food.created_at
                FROM foods food
                INNER JOIN
                food_types food_type ON
                food.food_type_id = food_type.id
                WHERE food.created_by_user_type<>0 AND food.created_at < date
                ORDER BY food.created_at DESC
                LIMIT perPage;

            ELSE
            SELECT food.id, food.created_by as member_id, food.food_type_id, food_type.food_type_name, food.food_name, food.measure, food.calories, food.serving_size, food.serving_unit, food.created_at
                FROM foods food
                INNER JOIN
                food_types food_type ON
                food.food_type_id = food_type.id
                WHERE food.created_by_user_type<>0 AND food.created_at < date AND food.food_name LIKE CONCAT(searchFilter COLLATE utf8_unicode_ci,"%")
                ORDER BY food.created_at DESC
                LIMIT perPage;

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
        DB::unprepared('DROP PROCEDURE IF EXISTS getFoods');
    }

}
