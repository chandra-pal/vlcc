<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberPackageImagesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_package_images', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->string('package_id', 50)->index();
            $table->string('package_name', 50)->index();
            $table->date('package_validity')->index();
            $table->string('before_image', 50)->index();
            $table->string('after_image', 50)->index();
            $table->foreign('member_id')->references('id')->on('members');
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
        Schema::table('member_package_images', function($table) {
            $table->dropForeign('member_package_images_member_id_foreign');
            $table->dropIndex('member_package_images_member_id_foreign');
            $table->dropColumn('member_id');
        });
        Schema::drop('member_package_images');
    }

}
