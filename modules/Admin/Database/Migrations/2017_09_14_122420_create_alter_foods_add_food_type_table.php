<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterFoodsAddFoodTypeTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('foods', function (Blueprint $table) {
            $table->integer('food_type_id')->unsigned()->nullable()->after('id');
            $table->foreign('food_type_id')->references('id')->on('food_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('foods', function($table) {
        });
    }
}
