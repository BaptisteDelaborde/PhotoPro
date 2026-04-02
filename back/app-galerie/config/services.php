<?php

use Psr\Container\ContainerInterface;
use photopro\core\application\usecases\StorageService;
use photopro\core\application\usecases\ServiceGalerie;
use photopro\core\application\ports\api\ServiceGalerieInterface;
use photopro\core\application\ports\spi\NotificationPublisherInterface;
use photopro\infra\repositories\PDOGalerieRepository;
use photopro\infra\messaging\RabbitMQPublisher;
use photopro\core\application\ports\spi\repositoryInterfaces\GalerieRepositoryInterface;
use photopro\api\providers\JWTManager;

use Aws\S3\S3Client;

return [
    PDO::class => function (ContainerInterface $c) {
        $host = $_ENV['DB_HOST'] ?? 'galerie.db';
        $db   = $_ENV['POSTGRES_DB'] ?? 'galerie';
        $user = $_ENV['POSTGRES_USER'] ?? 'galerie_user';
        $pass = $_ENV['POSTGRES_PASSWORD'] ?? 'galerie_password';
        
        $dsn = "pgsql:host=$host;dbname=$db"; 
        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    },

    GalerieRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOGalerieRepository($c->get(PDO::class));
    },

    NotificationPublisherInterface::class => function (ContainerInterface $c) {
        return new RabbitMQPublisher(
            host: $_ENV['RABBITMQ_HOST'] ?? 'rabbitmq',
            port: (int) ($_ENV['RABBITMQ_PORT'] ?? 5672),
            user: $_ENV['RABBITMQ_USER'] ?? 'photo',
            password: $_ENV['RABBITMQ_PASS'] ?? 'photo'
        );
    },

    ServiceGalerieInterface::class => function (ContainerInterface $c) {
        return new ServiceGalerie(
            $c->get(GalerieRepositoryInterface::class),
            $c->get(NotificationPublisherInterface::class)
        );
    },

    JWTManager::class => function (ContainerInterface $c) {
        $secretKey = $_ENV['JWT_SECRET_KEY'] ?? 'secret_par_defaut';
        return new JWTManager($secretKey, 'HS512');
    },
];