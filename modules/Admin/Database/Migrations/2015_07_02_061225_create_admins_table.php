<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('admins', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('username', 50)->unique()->index();
            $table->string('email', 100)->unique()->index();
            $table->string('first_name', 60)->index();
            $table->string('last_name', 60)->index();
            $table->string('password', 100);
            $table->boolean('gender')->default(true)->unsigned()->comment = "1 : Male, 0 : Female";
            $table->string('contact', 20)->comment = "Contact number either phone or mobile";
            $table->string('avatar', 255)->comment = "User profile picture";
            $table->boolean('status')->default(true)->unsigned()->index()->comment = "1 : Active, 0 : Inactive";
            $table->boolean('skip_ip_check')->unsigned()->default(false)->comment = "1 : Skip IP Check, 0 : Check IP";
            $table->integer('user_type_id')->unsigned()->index();
            $table->rememberToken();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('admins');
    }

}
