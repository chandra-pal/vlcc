<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterMemberSessionRecordAddServiceExecutionStatusTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_session_record', function(Blueprint $table) {
            $table->text('service_execution_status')->after('service_executed');
            $table->integer('service_executed')->change()->comment = "0: Not Executed, 1: Pending, 2: Failed, 3:Success";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
    }
}
