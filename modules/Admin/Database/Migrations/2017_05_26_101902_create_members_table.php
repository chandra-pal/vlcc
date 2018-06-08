<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('members', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('crm_customer_id', 20)->index();
            $table->string('first_name', 50)->index();
            $table->string('last_name', 50)->index();
            $table->string('email', 100)->index();
            $table->string('mobile_number', 20)->index();
            $table->string('app_version', 30)->index();
            $table->boolean('registered_from')->unsigned();
            $table->integer('diet_plan_id')->unsigned();
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
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
        Schema::drop('members');
    }

}
