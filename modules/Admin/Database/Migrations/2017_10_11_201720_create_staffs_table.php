<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staffs', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('first_name', 60)->index();
            $table->string('last_name', 60)->index();
            $table->boolean('gender')->default(true)->unsigned()->comment = "1 : Male, 0 : Female";
            $table->string('mobile_number', 20)->comment = "Contact number either phone or mobile";
            $table->boolean('status')->default(true)->unsigned()->index()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('staffs');
    }

}
