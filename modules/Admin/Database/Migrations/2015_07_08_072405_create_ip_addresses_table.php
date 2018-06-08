<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpAddressesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('ip_addresses', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('ip_address', 15)->unique()->index();
            $table->boolean('status')->default(true)->unsigned()->comment = "0 : Pending, 1 : Accepted, 2 : Rejected";
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
    public function down() {
        Schema::drop('ip_addresses');
    }

}
