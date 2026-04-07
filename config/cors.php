<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Options
    |--------------------------------------------------------------------------
    |
    | The allowed_origins, allowed_headers and allowed_methods options are case-insensitive.
    |
    | You don't need to provide both allowed_origins and allowed_origins_patterns.
    | If one of the strings passed matches, it is considered a valid origin.
    |
    | If ['*'] is provided to allowed_methods and allowed_headers, all methods and headers are allowed.
    |
    */

    'paths' => ['*/api/*'],  // Adjust this to your API routes or other paths

    'allowed_methods' => ['*'],  // Allow all methods

    'allowed_origins' => ['http://localhost:3000', 'https://primejapan.vercel.app'],  // Allow your Next.js app

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],  // Allow all headers

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
