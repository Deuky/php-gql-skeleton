<?php

return [
    [
        'name' => 'default',
        'config' => [
            'driver' => 'mysql',
            'host' => $_ENV['DB_HOST'],
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'port' => $_ENV['DB_PORT'] ?? null,
            'unix_socket' => $_ENV['DB_SOCKET'] ?? null,
        ]
    ]
];
