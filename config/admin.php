<?php

return [
    'name' => 'Admin',
    'app_server' => env('APP_SERVER', 'IN'),
    'api_base_url' => env('API_BASE_URL', 'http://45.127.101.98:8000/'),
    'crm_base_url_dev' => env('CRM_BASE_URL_DEV', 'http://13.67.48.41:8005/api/slimmer/'),
    'crm_base_url_prod' => env('CRM_BASE_URL_PROD', 'http://13.67.48.41:8008/api/slimmer/'),
    'asset_url' => env('ASSET_URL', 'http://45.127.101.98/vlcc-admin/public/'),
    'send_message_url' => env('SEND_MESSAGE_URL', 'http://bulkpush.mytoday.com/BulkSms/SingleMsgApi'),
    'feed_id' => env('FEED_ID', '360644'),
    'user_name' => env('USER_NAME', '9818682379'),
    'password' => env('PASSWORD', 'mwpta'),
    'sender_id' => env('SENDER_ID', '1'),
    'auth' => [
        'admin_model' => \Modules\Admin\Models\User::class,
        'table' => 'admins',
        'password' => ['email' => 'admin::emails.auth.password'],
    ],
    'filemanager' => [
        'url' => 'admin/filemanager/show',
        'url-files' => '/public/admintheme/filemanager/userfiles/',
    ],
    'settings' => [
    ],
    'database' => [
        'connections' => [
            'mysql' => [
                'driver' => 'mysql',
                'host' => env('ADMIN_DB_HOST', 'localhost'),
                'database' => env('ADMIN_DB_DATABASE', 'iplaravel'),
                'username' => env('ADMIN_DB_USERNAME', 'web'),
                'password' => env('ADMIN_DB_PASSWORD', 'websa'),
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
                'strict' => false,
            ]
        ]
    ]
];
