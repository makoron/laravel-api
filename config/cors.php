<?php

return [
    'paths' => ['api/*'],               // API配下だけCORS許可
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',        // ローカルNext
        'http://localhost:3001',        // ローカルNext（ポート違い）
        'https://next-app-peach-ten.vercel.app', // 本番を追加する場合
    ],
    'allowed_origins_patterns' => [
        '#^https://[a-z0-9-]+\.vercel\.app$#i', // 保険で入れておくと安心],
    ],
    'allowed_headers' => ['*'],         // Authorization を含め全部許可
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,    // Cookie使わないので false
];
