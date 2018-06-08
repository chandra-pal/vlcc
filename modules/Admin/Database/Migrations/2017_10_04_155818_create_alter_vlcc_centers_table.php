<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlterVlccCentersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('vlcc_centers', function(Blueprint $table) {
            $table->string('center_name', 255)->nullable()->after('id');
            $table->integer('area_id')->unsigned()->nullable()->after('area');
            $table->string('crm_center_id', 255)->nullable()->after('area_id');
            $table->string('crm_area_id', 255)->nullable()->after('crm_center_id');
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
