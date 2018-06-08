<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiOauthJwtTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_jwt', function(Blueprint $table) {
            $table->string('client_id', 100);
            $table->string('subject', 80)->nullable();
            $table->string('public_key', 2000)->nullable();
            $table->primary('client_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('oauth_jwt');
    }
}
