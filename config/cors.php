<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Configuration
    |--------------------------------------------------------------------------
    |
    | ここではフロントエンド（Next.js / Vercel / Localhost）からの
    | クロスオリジンアクセスを制御します。
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',    // 開発用 Next.js
        'https://*.vercel.app',     // Vercel デプロイ
        'https://igadon.xsrv.jp',   // Xserver 上の Laravel API
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
