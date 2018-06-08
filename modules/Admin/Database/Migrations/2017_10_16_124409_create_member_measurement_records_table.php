<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberMeasurementRecordsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_measurement_records', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->unsigned();
            $table->boolean('type')->unsigned()->comment = "1 : Arm, 2 : Tummy, 3 : Hip, 4: Thigh, 5: Chest, 6: Sides, 7: Back";
            $table->boolean('sub_type')->unsigned();
            $table->integer('value')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('member_id')->references('id')->on('members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
         Schema::table('member_measurement_records', function($table) {
            $table->dropForeign('member_measurement_records_member_id_foreign');
            $table->dropIndex('member_measurement_records_member_id_foreign');
        });
        Schema::drop('member_measurement_records');
    }

}
