<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberBcaDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_bca_details', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->date('recorded_date');
            $table->decimal('body_mass_index', 10, 2);
            $table->decimal('basal_metabolic_rate',  10, 2);
            $table->decimal('fat_weight', 10, 2);
            $table->decimal('fat_percent', 10, 2);
            $table->decimal('lean_body_mass_weight', 10, 2);
            $table->decimal('lean_body_mass_percent', 10, 2);
            $table->decimal('water_weight', 10, 2);
            $table->decimal('water_percent', 10, 2);
            $table->decimal('visceral_fat_level', 10, 2);
            $table->decimal('visceral_fat_area', 10, 2);
            $table->decimal('target_weight', 10, 2);
            $table->decimal('target_fat_percent', 10, 2);
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
        Schema::table('member_bca_details', function($table) {
            $table->dropForeign('member_bca_details_member_id_foreign');
            $table->dropIndex('member_bca_details_member_id_foreign');
            $table->dropColumn('member_id');

            $table->dropForeign('member_bca_details_package_id_foreign');
            $table->dropIndex('member_bca_details_package_id_foreign');
            $table->dropColumn('package_id');
        });
        Schema::drop('member_bca_details');
    }

}
