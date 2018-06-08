<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMachineAvailabilityTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('machine_availability', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('center_id')->unsigned();
            $table->integer('machine_id')->unsigned();
            $table->date('availability_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('carry_forward_availability')->default(false)->unsigned()->comment = "0 : No, 1 : Yes";
            $table->integer('carry_forward_availability_days')->default(0)->unsigned()->comment = "If carry_forward_availability is 1 then it will be the number of days the availability can get carry forworded;";
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('center_id')->references('id')->on('vlcc_centers');
            $table->foreign('machine_id')->references('id')->on('machines');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('machine_availability', function($table) {
            $table->dropForeign('machine_availability_machine_id_foreign');
            $table->dropIndex('machine_availability_machine_id_foreign');
            $table->dropColumn('machine_id');

            $table->dropForeign('machine_availability_center_id_foreign');
            $table->dropIndex('machine_availability_center_id_foreign');
            $table->dropColumn('center_id');
        });
        Schema::drop('machine_availability');
    }
}
