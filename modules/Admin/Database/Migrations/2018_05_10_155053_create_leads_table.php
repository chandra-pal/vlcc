<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('lead_name', 50)->unique()->index();
            $table->string('mobile_number', 30)->unique()->index();
            $table->string('crm_center_id', 255);
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
        Schema::drop('leads');
    }

}
