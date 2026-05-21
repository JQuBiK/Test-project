<?php

declare(strict_types=1);

$root = dirname(__DIR__);

return [
    'db' => [
        'host'     => getenv('DB_HOST') ?: '127.0.0.1',
        'port'     => (int) (getenv('DB_PORT') ?: 3306),
        'name'     => getenv('DB_NAME') ?: 'blog',
        'user'     => getenv('DB_USER') ?: 'blog',
        'password' => getenv('DB_PASSWORD') ?: 'blog',
        'charset'  => 'utf8mb4',
    ],

    'app' => [
        'debug'             => filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOL),
        'articles_per_page' => 6,
        'latest_per_block'  => 3,
        'similar_count'     => 3,
    ],

    'paths' => [
        'templates' => $root . '/templates',
        'compiled'  => $root . '/var/templates_c',
        'cache'     => $root . '/var/cache',
    ],
];
