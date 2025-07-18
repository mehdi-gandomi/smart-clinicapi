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
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'auth/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
// 'allowed_origins' => ["*"],
    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Content-Type',
        'X-Auth-Token',
        'Origin',
        'Authorization',
        'Accept',
        'X-Requested-With',
        'x-doctor-name',
        'X-Doctor-Name'
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
