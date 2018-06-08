<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTrGetVlccCenter extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetVlccCenters;
        CREATE PROCEDURE trGetVlccCenters(IN lastUpdatedAt TIMESTAMP)
        BEGIN        
            IF lastUpdatedAt IS NULL THEN            
                SELECT vc.id,vc.address,vc.area,ct.name AS city,st.name AS state, cn.name AS country,vc.pincode, 
                vc.latitude,vc.longitude,vc.phone_number,
                DATE_FORMAT(vc.created_at, "%d-%m-%Y %H:%i:%s") as created_at,
                DATE_FORMAT(vc.updated_at, "%d-%m-%Y %H:%i:%s") as updated_at                  
                FROM vlcc_centers AS vc  LEFT JOIN cities AS ct ON ct.id = vc.city_id    
                LEFT JOIN states AS st ON st.id = vc.state_id            
                LEFT JOIN countries AS cn ON cn.id = vc.country_id     
                WHERE vc.status = 1            
                ORDER BY COALESCE(vc.updated_at,vc.created_at);                   
            ELSE            
                SELECT vc.id,vc.address,vc.area,ct.name AS city,st.name AS state, cn.name AS country,vc.pincode,    
                vc.latitude,vc.longitude,vc.phone_number,
                DATE_FORMAT(vc.created_at, "%d-%m-%Y %H:%i:%s") as created_at,
                DATE_FORMAT(vc.updated_at, "%d-%m-%Y %H:%i:%s") as updated_at
                FROM vlcc_centers AS vc            
                LEFT JOIN cities AS ct ON ct.id = vc.city_id            
                LEFT JOIN states AS st ON st.id = vc.state_id            
                LEFT JOIN countries AS cn ON cn.id = vc.country_id            
                WHERE vc.status = 1 AND vc.updated_at > lastUpdatedAt            
                ORDER BY COALESCE(vc.updated_at,vc.created_at);       
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
        DB::unprepared('DROP PROCEDURE IF EXISTS trGetVlccCenters');
    }
}
