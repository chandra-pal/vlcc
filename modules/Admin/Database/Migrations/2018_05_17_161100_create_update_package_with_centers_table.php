<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdatePackageWithCentersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_packages', function(Blueprint $table) {
            $table->string('crm_center_id',255)->after('crm_package_id')->comment="Package wise crm_center_id";
            $table->boolean('status')->default(true)->unsigned()->index()->comment = "1 : Active, 0 : Inactive";            
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
