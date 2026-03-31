<?php

use Psr\Container\ContainerInterface;
use photopro\core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;
use photopro\infra\repositories\PDOAuthReposiroty;

return [
    'db' => [
        'auth' => [
            'driver'   => 'pgsql',
            // 'auth.db' correspond au nom du service dans ton docker-compose.yml
            'host'     => $_ENV['AUTH_DB_HOST'] ?? 'auth.db',
            'port'     => $_ENV['AUTH_DB_PORT'] ?? 5432,
            // Ces valeurs devront correspondre à ce que tu vas mettre dans ton fichier authdb.env
            'dbname'   => $_ENV['AUTH_DB_NAME'] ?? 'photopro_auth',
            'user'     => $_ENV['AUTH_DB_USER'] ?? 'photopro',
            'password' => $_ENV['AUTH_DB_PASS'] ?? 'photopro',
        ]
    ],

    // Options PDO communes
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
