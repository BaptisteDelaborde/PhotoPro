<?php

use Psr\Container\ContainerInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;
use photopro\infra\repositories\PDOAuthReposiroty;

return [
    'db' => [
        'auth' => [
            'driver'   => 'pgsql',
            'host'     => $_ENV['AUTH_DB_HOST'] ?? 'auth.db',
            'port'     => $_ENV['AUTH_DB_PORT'] ?? 5432,
            'dbname'   => $_ENV['POSTGRES_DB'] ?? 'auth',
            'user'     => $_ENV['POSTGRES_USER'] ?? 'auth_user',
            'password' => $_ENV['POSTGRES_PASSWORD'] ?? 'auth_password',
        ]
    ],

    'pdo_options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],

    \PDO::class => function (ContainerInterface $c) {
        $db = $c->get('db')['auth'];
        $options = $c->get('pdo_options');
        $dsn = "{$db['driver']}:host={$db['host']};port={$db['port']};dbname={$db['dbname']}";
        return new \PDO($dsn, $db['user'], $db['password'], $options);
    },
    AuthRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOAuthReposiroty($c->get(\PDO::class));
    }
];
