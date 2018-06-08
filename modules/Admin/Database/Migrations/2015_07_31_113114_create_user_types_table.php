<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTypesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('user_types', function(Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('name')->unique()->index();
            $table->string('description')->nullable();
            $table->boolean('priority')->default(1)->unsigned()->comment = "Between 1 to 10";
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('user_types');
    }

}
