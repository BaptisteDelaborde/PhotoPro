<?php

use DI\Container;
use photopro\api\providers\AuthnProviderInterface;
use photopro\api\providers\JWTAuthnProvider;
use photopro\api\providers\JWTManager;
use photopro\core\application\ports\api\ServiceUserInterface;
use photopro\core\application\usecases\ServiceUser;

return [
    ServiceUserInterface::class => function (Container $container) {
        $authRepo = $container->get(\photopro\core\application\ports\spi\repositoryInterfaces\AuthRepositoryInterface::class);
        return new ServiceUser($authRepo);
    },
    JWTManager::class => function() {
        $secretKey = $_ENV['JWT_SECRET'] ?? 'default-secret'; 
        return new JWTManager($secretKey, 'HS512');
    },
    AuthnProviderInterface::class => function (Container $container) {
        $JWTmanager = $container->get(JWTManager::class);
        $serviceUser = $container->get(ServiceUserInterface::class);
        return new JWTAuthnProvider($JWTmanager,$serviceUser);
    },
];
