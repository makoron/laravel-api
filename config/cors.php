<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],   // ← 全メソッド許可
    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:3001',
        'https://igadon.xsrv.jp',
        'https://next-app-peach-ten.vercel.app',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],   // ← 全ヘッダ許可（Authorization を含む）
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
