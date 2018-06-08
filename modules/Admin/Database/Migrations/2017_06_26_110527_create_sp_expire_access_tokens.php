<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpExpireAccessTokens extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS expireAccessTokens;
        CREATE PROCEDURE expireAccessTokens(IN mobileNumber VARCHAR(255), IN clientId VARCHAR(80))
        BEGIN
            DECLARE appGroup INT;
            DECLARE affected_rows VARCHAR(5);
            SET appGroup = (SELECT app_group FROM oauth_clients WHERE client_id=clientId COLLATE utf8_unicode_ci);
            
            UPDATE oauth_access_tokens oat INNER JOIN oauth_clients oc ON oc.client_id = oat.client_id 
            SET oat.expires=CURRENT_TIMESTAMP() WHERE oc.app_group = appGroup 
            AND oat.user_id=mobileNumber COLLATE utf8_unicode_ci 
            AND oat.expires > CURRENT_TIMESTAMP();

            IF(ROW_COUNT() >0) THEN 
                 SELECT 1 AS affected_rows;
            ELSE 
              SELECT 0 AS affected_rows;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS expireAccessTokens');
    }
}
