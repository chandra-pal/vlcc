<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterFoodsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->integer('created_by_user_type')->unsigned()->comment("0 : In case of Foods added by customer from App, Other : User type id for admin Users")->after('serving_unit');
            $table->integer('created_by')->unsigned()->change()->comment("Referring to Column ID of Members or Admin table");
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
            //$table->dropColumn('created_by_user_type');
        });
    }

}
