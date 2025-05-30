<?php

$boolean = function (mixed $value): bool {
    return in_array($value, ['true', 1, '1', true, 'yes'], true);
};

$appSnakeName = strtolower(str_replace(' ', '_', $_ENV['APP_NAME']));
$isDebug = $boolean($_ENV['APP_DEBUG'] ?? 0);

return [
    'app' => [
        'name' => $_ENV['APP_NAME'],
        'version' => $_ENV['APP_VERSION'],
    ],
    'session' => [
        'name'       => $appSnakeName . '_session',
        'flash_name' => $appSnakeName . '_flash',
        'secure'     => false,
        'httponly'   => false,
        'samesite'   => 'lax',
    ],
    'logger' => [
        'name' => 'app',
        'path' => PATH_STORAGE_FOLDER . '/var/app.log',
        'max_files' => 30,
        'request_id_header_name' => 'X-Request-Id'
    ],
    'slim' => [
        'base_path' => '/api',
        'display_error_details' => $isDebug,
        'log_errors' =>  $isDebug,
        'log_error_details' => $isDebug,
    ],
    'doctrine' => [
        'dev_mode' => $isDebug,
        'cache_dir'  => PATH_STORAGE_FOLDER . '/cache/doctrine',
        'entity_dir' => [PATH_APP_FOLDER . '/Entity'],
        'connection' => [
            'driver' => $_ENV['DATABASE_DRIVER'],
            'host' => $_ENV['DATABASE_HOST'],
            'dbname' => $_ENV['DATABASE_NAME'],
            'user' => $_ENV['DATABASE_USER'],
            'password' => $_ENV['DATABASE_PASS']
        ]
    ]
];
