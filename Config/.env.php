<?php
// Configuração exclusiva para PostgreSQL
return [
    'db' => [
        'driver' => 'pgsql',
        'host' => 'localhost',
        'port' => '5432',
        'database' => 'farmacia',
        'username' => 'postgres',
        'password' => '1234',
        'schema' => 'public',
    ],
    'app' => [
        // Ajuste se necessário; não é usado diretamente no roteador atual
        'base_url' => '/Site-Farmacia_em-PHP/',
    ],
];