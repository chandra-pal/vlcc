<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrimmedMemberDeviceTokensTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('trimmed_member_device_tokens', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('trimmed_member_id')->unsigned();
            $table->text('device_token', 50);
            $table->boolean('device_type')->unsigned()->comment = "1: Android, 2: iOS, 3: Web";
            $table->boolean('status')->unsigned()->comment = "1 : Active, 0 : Inactive, 2 : Deleted";
            $table->foreign('trimmed_member_id')->references('id')->on('trimmed_members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('trimmed_member_device_tokens', function($table) {
            $table->dropForeign('trimmed_member_device_tokens_trimmed_member_id_foreign');
            $table->dropIndex('trimmed_member_device_tokens_trimmed_member_id_foreign');
            $table->dropColumn('trimmed_member_id');
        });
        Schema::drop('trimmed_member_device_tokens');
    }

}
