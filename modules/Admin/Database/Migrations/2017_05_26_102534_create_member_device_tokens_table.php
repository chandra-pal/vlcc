<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberDeviceTokensTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('member_device_tokens', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->text('device_token', 50);
            $table->boolean('device_type')->unsigned()->comment = "1: Android, 2: iOS, 3: Web";
            $table->boolean('status')->unsigned()->comment = "1 : Active, 0 : Inactive, 2 : Deleted";
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
        Schema::table('member_device_tokens', function($table) {
            $table->dropForeign('member_device_tokens_member_id_foreign');
            $table->dropIndex('member_device_tokens_member_id_foreign');
            $table->dropColumn('member_id');
        });
        Schema::drop('member_device_tokens');
    }

}
