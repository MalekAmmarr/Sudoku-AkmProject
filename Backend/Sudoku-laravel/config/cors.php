<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:8080', 'http://localhost:4200','http://127.0.0.1:8000','http://127.0.0.1:8080'],
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'Accept'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];

