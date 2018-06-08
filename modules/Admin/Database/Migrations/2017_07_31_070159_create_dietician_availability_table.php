<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDieticianAvailabilityTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('dietician_availability', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('dietician_id')->unsigned();
            $table->date('availability_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('break_time');
            $table->boolean('carry_forward_availability')->default(false)->unsigned()->comment = "0 : No, 1 : Yes";
            $table->integer('carry_forward_availability_days')->default(0)->unsigned()->comment = "If carry_forward_availability is 1 then it will be the number of days the availability can get carry forworded;";
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('dietician_id')->references('id')->on('admins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('dietician_availability', function($table) {
            $table->dropForeign('dietician_availability_dietician_id_foreign');
            $table->dropIndex('dietician_availability_dietician_id_foreign');
            $table->dropColumn('dietician_id');
        });
        Schema::drop('dietician_availability');
    }

}
