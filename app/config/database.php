<?php
return [
    'default' => 'pgsql',

    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => getenv('POSTGRES_HOST') ?: '127.0.0.1',
            'port' => getenv('POSTGRES_PORT') ?: '5432',
            'database' => getenv('POSTGRES_DB') ?: 'db',
            'username' => getenv('POSTGRES_USER') ?: null,
            'password' => getenv('POSTGRES_PASSWORD') ?: null,
            //'options' => [],
        ],
    ]
];