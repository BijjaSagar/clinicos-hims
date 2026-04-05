<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(array_merge([
        'http://localhost:5173',
        'http://localhost:3000',
    ], explode(',', env('CORS_ALLOWED_ORIGINS', 'https://clinic0s.com,https://www.clinic0s.com')))),

    'allowed_origins_patterns' => [
        env('CORS_ORIGIN_PATTERN', 'https://*.clinic0s.com'),
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
