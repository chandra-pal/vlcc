<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberPackagesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_packages', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->string('crm_package_id', 255);
            $table->string('package_title', 255);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('height', 10, 2);
            $table->decimal('weight', 10, 2);
            $table->decimal('waist', 10, 2);
            $table->decimal('total_payment', 14, 2);
            $table->decimal('payment_made', 14, 2);
            $table->decimal('package_start_weight', 10, 2);
            $table->decimal('package_target_weight', 10, 2);
            $table->text('conversion');
            $table->text('programme_re_booked');
            $table->text('remarks');
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
        Schema::table('member_packages', function($table) {
            $table->dropForeign('member_packages_member_id_foreign');
            $table->dropIndex('member_packages_member_id_foreign');
            $table->dropColumn('member_id');
        });
        Schema::drop('member_packages');
    }

}
