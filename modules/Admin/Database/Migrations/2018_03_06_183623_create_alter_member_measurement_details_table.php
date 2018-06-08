<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterMemberMeasurementDetailsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_measurement_details', function (Blueprint $table) {
            $table->integer('session_id')->unsigned()->nullable()->after('member_id');
            $table->decimal('arms', 10, 2)->change()->comment("Arms Left");
            $table->decimal('arm_right', 10, 2)->after('arms')->comment("Arms Right");
            $table->decimal('thighs', 10, 2)->change()->comment("Thighs Left");
            $table->decimal('thighs_right', 10, 2)->after('thighs')->comment("Thighs Right");
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
