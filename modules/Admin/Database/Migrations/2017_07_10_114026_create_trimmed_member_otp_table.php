<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrimmedMemberOtpTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('trimmed_member_otp', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('mobile_number', 10)->index();
            $table->string('otp', 4)->index();
            $table->boolean('sms_delivered')->default(true)->comment = "1 : Yes, 0 : No";
            $table->string('error_message', 200);
            $table->boolean('otp_used')->unsigned()->comment = "1 : Yes, 0 : No";
            $table->boolean('platform_generated_for')->unsigned()->comment = "1 : Android, 2 : iOS, 3 : WebPOS, 4 : Website";
            $table->boolean('attempt_count')->unsigned()->comment = "Temprory Block member after 3 invalid attempt for next 15 mins";
            $table->boolean('otp_generated_for')->unsigned()->comment = "201 : Login, 202 : Registration, 203 : Card Registration, 204 : Order Redemption ";
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('trimmed_member_otp');
    }

}
