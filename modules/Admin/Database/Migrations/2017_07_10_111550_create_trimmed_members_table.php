<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrimmedMembersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trimmed_members', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('first_name', 50)->index();
            $table->string('last_name', 50)->index();
            $table->string('email', 100)->index();
            $table->date('dob');
            $table->smallInteger('weight');
            $table->smallInteger('height');
            $table->boolean('gender')->default(true)->unsigned()->comment = "1: Male, 2: Female";
            $table->string('mobile_number', 20)->index();
            $table->string('app_version', 30)->index();
            $table->boolean('registered_from')->unsigned()->comment = "1: Android, 2: iOS, 3: Web Portal";
            $table->integer('trimmed_diet_plan_id')->unsigned();
            $table->boolean('status')->default(true)->unsigned()->comment = "1: Active, 2: Inactive, 3: Blocked, 4: Temprory Blocked";
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
        Schema::drop('trimmed_members');
    }
}
