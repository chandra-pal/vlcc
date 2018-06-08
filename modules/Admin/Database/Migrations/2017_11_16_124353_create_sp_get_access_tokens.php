<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpGetAccessTokens extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS getAccessTokens;
        CREATE PROCEDURE getAccessTokens(IN mobileNumber VARCHAR(255), IN clientId VARCHAR(80))
        BEGIN            
            DECLARE appGroup INT;
            
            SET appGroup = (SELECT app_group FROM oauth_clients WHERE client_id=clientId COLLATE utf8_unicode_ci);
            
            SELECT tokens.access_token as token,clients.app_group  
            FROM oauth_access_tokens tokens INNER JOIN oauth_clients clients 
            ON tokens.client_id = clients.client_id 
            WHERE tokens.user_id=mobileNumber COLLATE utf8_unicode_ci 
            AND tokens.expires > CURRENT_TIMESTAMP() 
            AND clients.app_group=appGroup;                
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
        DB::unprepared('DROP PROCEDURE IF EXISTS getAccessTokens');
    }
}
