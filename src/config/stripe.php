<?php
return [
    'stripe' => [
        'public' => env('STRIPE_KEY', 'pk_test_default'), // ← `null` の場合はデフォルト値を返す
        'secret' => env('STRIPE_SECRET', 'sk_test_default'),
    ],
];

