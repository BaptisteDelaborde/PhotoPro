<?php

use Psr\Container\ContainerInterface;
use photopro\api\actions\UploadAction;
use photopro\api\actions\CreateGalerieAction;
use photopro\api\middleware\AuthnMiddleware;
use photopro\core\application\usecases\StorageService;
use photopro\core\application\ports\api\ServiceGalerieInterface;
use photopro\api\providers\JWTManager;

return [
    UploadAction::class => function (ContainerInterface $c) {
        return new UploadAction(
            $c->get(StorageService::class),
            $c->get(ServiceGalerieInterface::class)
        );
    },
    
    CreateGalerieAction::class => function (ContainerInterface $c) {
        return new CreateGalerieAction($c->get(ServiceGalerieInterface::class));
    },

    // Middlewares
    AuthnMiddleware::class => function (ContainerInterface $c) {
        $secretKey = $_ENV['JWT_SECRET_KEY'] ?? 'cle_secrete';
        return new AuthnMiddleware($secretKey);
    }
];