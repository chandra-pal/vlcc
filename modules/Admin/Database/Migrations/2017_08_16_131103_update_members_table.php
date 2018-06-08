<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMembersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('members', function(Blueprint $table) {
            if (!Schema::hasColumn('members', 'dietician_username')) {
                $table->string('dietician_username', 50)->after('crm_customer_id');
            }
            if (Schema::hasColumn('members', 'dietician_id')) {
                $table->dropColumn('dietician_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('members', function(Blueprint $table) {
            $table->dropColumn('dietician_username');
        });
    }

}
