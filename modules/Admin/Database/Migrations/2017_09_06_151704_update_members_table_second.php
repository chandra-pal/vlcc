<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMembersTableSecond extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('members', function(Blueprint $table) {
            $table->string('alternate_phone_number', 15)->after('mobile_number');
            $table->tinyInteger('gender')->after('diet_plan_id')->comment = '1 : Male, 2 : Female';
            $table->date('date_of_birth')->after('gender');
            $table->text('address')->after('date_of_birth');
            $table->string('profession', 50)->after('address');
            $table->string('family_physician_name', 50)->after('profession');
            $table->string('family_physician_number', 50)->after('family_physician_name');
            $table->text('existing_medical_problem')->after('family_physician_number');
            $table->text('therapies')->after('existing_medical_problem');
            $table->text('services_to_be_avoided')->after('therapies');
            $table->string('category_code',50)->after('services_to_be_avoided');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('members', function(Blueprint $table) {
            
        });
    }

}
