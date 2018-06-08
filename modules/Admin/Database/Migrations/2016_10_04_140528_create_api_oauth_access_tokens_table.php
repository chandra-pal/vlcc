<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiOauthAccessTokensTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_access_tokens', function(Blueprint $table) {
            $table->string('access_token', 40);
            $table->string('client_id', 80);
            $table->string('user_id', 255)->nullable();
            $table->timestamp('expires');
            $table->string('scope', 2000)->nullable();
            $table->primary('access_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('oauth_access_tokens');
    }
}
