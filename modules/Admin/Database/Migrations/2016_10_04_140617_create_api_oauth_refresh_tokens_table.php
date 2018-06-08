<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiOauthRefreshTokensTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_refresh_tokens', function(Blueprint $table) {
            $table->string('refresh_token', 40);
            $table->string('client_id', 80);
            $table->string('user_id', 255)->nullable();
            $table->timestamp('expires');
            $table->string('scope', 2000)->nullable();
            $table->primary('refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('oauth_refresh_tokens');
    }
}
