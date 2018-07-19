<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterMemberSessionRecordAddCrmCenterId extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_session_record', function(Blueprint $table) {
            $table->string('crm_center_id', 255)->after('package_id')->comment="Center where session programme record was added.";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('');
    }

}
