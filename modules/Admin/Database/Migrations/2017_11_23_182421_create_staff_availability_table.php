<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffAvailabilityTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_availability', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('center_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->date('availability_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('break_time');
            $table->boolean('carry_forward_availability')->default(false)->unsigned()->comment = "0 : No, 1 : Yes";
            $table->integer('carry_forward_availability_days')->default(0)->unsigned()->comment = "If carry_forward_availability is 1 then it will be the number of days the availability can get carry forworded;";
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('center_id')->references('id')->on('vlcc_centers');
            $table->foreign('staff_id')->references('id')->on('admins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('staff_availability', function($table) {
            $table->dropForeign('staff_availability_staff_id_foreign');
            $table->dropIndex('staff_availability_staff_id_foreign');
            $table->dropColumn('staff_id');

            $table->dropForeign('staff_availability_center_id_foreign');
            $table->dropIndex('staff_availability_center_id_foreign');
            $table->dropColumn('center_id');
        });
        Schema::drop('staff_availability');
    }
}
