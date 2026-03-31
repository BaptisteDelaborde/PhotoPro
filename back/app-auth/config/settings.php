<?php

use Psr\Container\ContainerInterface;
use photopro\auth\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface;
use photopro\auth\infrastructure\repositories\PDOAuthRepository;

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

    // Connexion à la base de données Auth
    'db.auth' => function (ContainerInterface $c): PDO {
        $config = $c->get('db')['auth'];
        $options = $c->get('pdo_options');
        $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        return new PDO($dsn, $config['user'], $config['password'], $options);
    },

    // Repository Auth (Injection de la dépendance PDO)
    AuthRepositoryInterface::class => function (ContainerInterface $c) {
        // (J'en ai profité pour corriger la petite faute de frappe "Reposiroty" -> "Repository" au passage 😉)
        return new PDOAuthRepository($c->get('db.auth'));
    },
];