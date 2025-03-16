<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'favorites/*', 'checkout/*'], // ← checkout/* を追加
    'allowed_methods' => ['*'], // すべてのHTTPメソッドを許可
    'allowed_origins' => ['*'], // すべてのオリジン（ドメイン）を許可
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // すべてのヘッダーを許可
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];

