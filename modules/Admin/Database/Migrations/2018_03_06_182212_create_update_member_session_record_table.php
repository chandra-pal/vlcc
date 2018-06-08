<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateMemberSessionRecordTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_session_record', function(Blueprint $table) {
            $table->boolean('service_executed')->after('otp_verified')->unsigned()->comment = "CLM Service Execution Flag 1 : Yes, 0 : No";
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
