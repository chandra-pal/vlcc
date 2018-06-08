<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterMemberBcaDetailTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_bca_details', function(Blueprint $table) {
            $table->decimal('protein', 10, 2)->after('bca_image');
            $table->decimal('mineral', 10, 2)->after('protein');
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
