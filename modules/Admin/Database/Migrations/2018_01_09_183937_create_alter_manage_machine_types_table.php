<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterManageMachineTypesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('machines', function(Blueprint $table) {
            $table->integer('machine_type_id')->unsigned()->nullable()->after('id');
            $table->foreign('machine_type_id')->references('id')->on('machine_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('machines', function($table) {
            $table->dropForeign('machines_machine_type_id_foreign');
            $table->dropIndex('machines_machine_type_id_foreign');
        });
    }

}
