<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomAvailabilityTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('room_availability', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('center_id')->unsigned();
            $table->integer('room_id')->unsigned();
            $table->date('availability_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('carry_forward_availability')->default(false)->unsigned()->comment = "0 : No, 1 : Yes";
            $table->integer('carry_forward_availability_days')->default(0)->unsigned()->comment = "If carry_forward_availability is 1 then it will be the number of days the availability can get carry forworded;";
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('center_id')->references('id')->on('vlcc_centers');
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('room_availability', function($table) {
            $table->dropForeign('room_availability_room_id_foreign');
            $table->dropIndex('room_availability_room_id_foreign');
            $table->dropColumn('room_id');

            $table->dropForeign('room_availability_center_id_foreign');
            $table->dropIndex('room_availability_center_id_foreign');
            $table->dropColumn('center_id');
        });
        Schema::drop('room_availability');
    }

}
