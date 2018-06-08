<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVlccCentersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vlcc_centers', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->text('address');
            $table->string('area', 255)->index();
            $table->integer('city_id')->unsigned();
            $table->integer('state_id')->unsigned();
            $table->integer('country_id')->unsigned();
            $table->string('pincode', 10);
            $table->string('latitude', 255);
            $table->string('longitude', 255);
            $table->string('phone_number', 255);
            $table->boolean('status')->default(true)->unsigned()->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vlcc_centers', function($table) {
            $table->dropForeign('vlcc_centers_city_id_foreign');
            $table->dropIndex('vlcc_centers_city_id_foreign');
            $table->dropColumn('city_id');

            $table->dropForeign('vlcc_centers_state_id_foreign');
            $table->dropIndex('vlcc_centers_state_id_foreign');
            $table->dropColumn('state_id');

            $table->dropForeign('vlcc_centers_country_id_foreign');
            $table->dropIndex('vlcc_centers_country_id_foreign');
            $table->dropColumn('country_id');
        });
        Schema::drop('vlcc_centers');
    }
}
