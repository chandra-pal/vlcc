<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateMemberPackageServicesThird extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_package_services', function(Blueprint $table) {
            $table->string('service_category',20)->after('area_specification')->comment("100000001: Slimming, 100000002 : Beauty");
            $table->string('service_code',50)->after('service_category');
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
