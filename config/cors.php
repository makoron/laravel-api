<?php

return [
    'paths' => ['api', 'api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],

    // 明示的にローカルとXserverを許可
    'allowed_origins' => [
        'http://localhost:3000',
        'https://igadon.xsrv.jp',
    ],

    // Vercel の任意サブドメインを許可（例: https://your-app.vercel.app）
    'allowed_origins_patterns' => [
        '#^https://[a-z0-9-]+\.vercel\.app$#i',
    ],

    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,

    // Bearer運用でも true のままで問題なし（将来Cookie運用に切替えても安心）
    'supports_credentials' => true,
];
