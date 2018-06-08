<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterFoodsTableNew extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->boolean('status')->default(true)->unsigned()->comment("1 : Active, 0 : Inactive")->after("serving_unit");
            $table->dropUnique('foods_food_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('foods', function (Blueprint $table)
        {
            //$table->dropColumn('status');
        });
    }

}
