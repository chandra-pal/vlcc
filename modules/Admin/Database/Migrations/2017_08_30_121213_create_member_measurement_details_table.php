<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberMeasurementDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_measurement_details', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->date('recorded_date');
            $table->decimal('neck', 10, 2);
            $table->decimal('chest', 10, 2);
            $table->decimal('arms', 10, 2);
            $table->decimal('tummy', 10, 2);
            $table->decimal('waist', 10, 2);
            $table->decimal('hips', 10, 2);
            $table->decimal('thighs', 10, 2);
            $table->decimal('total_cm_loss', 10, 2);
            $table->string('therapist_name', 255);
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('package_id')->references('id')->on('member_packages');
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
        Schema::table('member_measurement_details', function($table) {
            $table->dropForeign('member_measurement_details_member_id_foreign');
            $table->dropIndex('member_measurement_details_member_id_foreign');
            $table->dropColumn('member_id');

            $table->dropForeign('member_measurement_details_package_id_foreign');
            $table->dropIndex('member_measurement_details_package_id_foreign');
            $table->dropColumn('package_id');
        });
        Schema::drop('member_measurement_details');
    }

}
