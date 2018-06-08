<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpDeleteRefreshToken extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS deleteRefreshTokens;
        CREATE PROCEDURE deleteRefreshTokens(IN mobileNumber VARCHAR(255), IN clientId VARCHAR(80))
        BEGIN
            IF EXISTS(SELECT refresh_token FROM oauth_refresh_tokens WHERE user_id=mobileNumber COLLATE utf8_unicode_ci AND client_id=clientId COLLATE utf8_unicode_ci) THEN 
                DELETE FROM oauth_refresh_tokens WHERE user_id=mobileNumber COLLATE utf8_unicode_ci AND client_id=clientId COLLATE utf8_unicode_ci;
            END IF;    
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS deleteRefreshTokens');
    }

}
