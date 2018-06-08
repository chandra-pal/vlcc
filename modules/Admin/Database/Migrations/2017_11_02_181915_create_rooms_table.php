<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('name', 200)->index();
            $table->integer('center_id')->unsigned()->index();
            $table->boolean('room_type')->default(1)->unsigned()->comment = "1 : Male, 2 : Female, 3:Common";
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('center_id')->references('id')->on('vlcc_centers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('rooms', function($table) {
            $table->dropForeign('rooms_center_id_foreign');
            $table->dropIndex('rooms_center_id_foreign');
            $table->dropColumn('center_id');
        });
        Schema::drop('rooms');
    }

}
