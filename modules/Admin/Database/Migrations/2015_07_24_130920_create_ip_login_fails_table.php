<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpLoginFailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('ip_login_fails', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('ip_address', 15)->index();
            $table->string('username');
            $table->dateTime('access_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('ip_login_fails');
    }

}
