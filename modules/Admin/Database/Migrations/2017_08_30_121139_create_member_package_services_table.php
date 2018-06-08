<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberPackageServicesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_package_services', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->string('crm_service_id', 255);
            $table->string('service_name', 255);
            $table->datetime('service_validity');
            $table->decimal('services_booked', 10, 2);
            $table->decimal('services_consumed', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
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
        Schema::table('member_package_services', function($table) {
            $table->dropForeign('member_package_services_member_id_foreign');
            $table->dropIndex('member_package_services_member_id_foreign');
            $table->dropColumn('member_id');

            $table->dropForeign('member_package_services_package_id_foreign');
            $table->dropIndex('member_package_services_package_id_foreign');
            $table->dropColumn('package_id');
        });
        Schema::drop('member_package_services');
    }

}
