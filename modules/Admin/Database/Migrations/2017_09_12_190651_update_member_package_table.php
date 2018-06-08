<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMemberPackageTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_packages', function(Blueprint $table) {
            $table->renameColumn('package_start_weight', 'programme_booked');
            $table->renameColumn('package_target_weight', 'programme_needed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_packages', function(Blueprint $table) {
            $table->renameColumn('programme_booked', 'package_start_weight');
            $table->renameColumn('programme_needed', 'package_target_weight');
        });
    }

}
