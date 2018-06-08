<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMemberPackagesTableThird extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_packages', function(Blueprint $table) {
            if (!Schema::hasColumn('member_packages', 'programme_booked_by')) {
                $table->string('programme_booked_by', 50)->before('programme_booked');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('member_packages', function(Blueprint $table) {
            
        });
    }

}
