<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMachineCentersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_centers', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('center_id')->unsigned();
            $table->integer('machine_id')->unsigned();
            $table->foreign('center_id')->references('id')->on('vlcc_centers');
            $table->foreign('machine_id')->references('id')->on('machines');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('machine_centers', function($table) {
            $table->dropForeign('machine_centers_machine_id_foreign');
            $table->dropIndex('machine_centers_machine_id_foreign');
            $table->dropColumn('machine_id');

            $table->dropForeign('machine_centers_center_id_foreign');
            $table->dropIndex('machine_centers_center_id_foreign');
            $table->dropColumn('center_id');
        });
        Schema::drop('machine_centers');
    }
}
