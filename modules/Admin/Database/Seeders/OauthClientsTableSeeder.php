<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon as Carbon;

class OauthClientsTableSeeder extends Seeder {

    public function run() {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('oauth_clients')->truncate();

        $data['client_id'] = 'web259ddd3ed8ff3843839b';
        $data['client_secret'] = '4c7f6f8fa93d59c45502c0ae8c4aweb';
        $data['redirect_uri'] = env('API_URL', '');
        DB::table('oauth_clients')->insert(
                $data
        );

        $data['client_id'] = 'ios259ddd3ed8ff3843839b';
        $data['client_secret'] = '4c7f6f8fa93d59c45502c0ae8c4aios';
        $data['redirect_uri'] = env('API_URL', '');
        DB::table('oauth_clients')->insert(
                $data
        );

        $data['client_id'] = 'android259ddd3ed8ff3843839b';
        $data['client_secret'] = '4c7f6f8fa93d59c45502c0aeandroid';
        $data['redirect_uri'] = env('API_URL', '');
        DB::table('oauth_clients')->insert(
                $data
        );
        
        $data['client_id'] = 'androidb01e315e1064c5d4a44b';
        $data['client_secret'] = 'da614fa61941b82ba5e9android';
        $data['redirect_uri'] = env('API_URL', '');
        DB::table('oauth_clients')->insert(
                $data
        );
        
        $data['client_id'] = 'iosb01e315e1064c5d4a44b';
        $data['client_secret'] = 'da614fa61941b82ba5e9ios';
        $data['redirect_uri'] = env('API_URL', '');
        DB::table('oauth_clients')->insert(
                $data
        );

        
        DB::table('oauth_access_tokens')->truncate();
        
        $dataToken['access_token'] = 'gauravpatel91e25054086223f773b9d15acc3c5';
        $dataToken['client_id'] = 'web259ddd3ed8ff3843839b';
        $dataToken['user_id'] = null;
        $dataToken['expires'] = Carbon::today()->addYear(21)->toDateTimeString();
        DB::table('oauth_access_tokens')->insert(
                $dataToken
        );

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
