<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateMemberPackageServicesSecondTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('member_package_services', function (Blueprint $table) {
            $table->text('area_specification')->after('crm_service_guid')->comment("Multiple comma separated area specifications");
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
