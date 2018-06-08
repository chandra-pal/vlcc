<?php
return [
    'name' => 'Admin',
    'api_base_url' => env('API_BASE_URL', 'http://192.168.151.144:8000/'),
    'crm_base_url' => 'http://13.67.48.41:8008/api/slimmer/',
    'clm_execution_base_url_dev' => env('CLM_EXECUTION_BASE_URL_DEV', 'http://13.67.48.41:8029/api/'),
    'clm_execution_base_url_prod' => env('CLM_EXECUTION_BASE_URL_PROD', 'http://13.67.48.41:8034/api/'),
    'auth' => [
        'admin_model' => \Modules\Admin\Models\User::class,
        'table' => 'admins',
        'password' => ['email' => 'admin::emails.auth.password'],
    ],
    'filemanager' => [
        'url' => 'admin/filemanager/show',
        'url-files' => '/public/admintheme/filemanager/userfiles/',
    ],
    'upload_path' => [
        'testimonials' => 'testimonials',
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
