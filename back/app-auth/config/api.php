<?php

use photopro\api\actions\SigninAction;
use photopro\api\actions\SignupAction;
use photopro\api\actions\ValidateTokenAction;
use photopro\api\actions\RefreshAction;
use photopro\api\middlewares\AuthnMiddleware;
use photopro\api\providers\AuthnProviderInterface;
use photopro\api\providers\JWTManager;
use photopro\api\actions\UpdatePhotographeAction;
use photopro\api\actions\GetPhotographeProfileAction;

return [
    SigninAction::class => function ($c) {
        return new SigninAction(
            $c->get(AuthnProviderInterface::class)
        );
    },
    SignupAction::class => function ($c) {
        return new SignupAction(
            $c->get(AuthnProviderInterface::class)
        );
    },
    ValidateTokenAction::class => function ($c) {
        return new ValidateTokenAction(
            $c->get(JWTManager::class)
        );
    },
    RefreshAction::class => function ($c) {
        return new RefreshAction(
            $c->get(AuthnProviderInterface::class)
        );
    },

    AuthnMiddleware::class => function ($c) {
        return new AuthnMiddleware(
            $c->get(JWTManager::class)
        );
    },

    UpdatePhotographeAction::class => function ($c) {
        return new UpdatePhotographeAction(
            $c->get(\photopro\core\application\ports\api\ServiceUserInterface::class)
        );
    },

    GetPhotographeProfileAction::class => function ($c) {
        return new GetPhotographeProfileAction(
            $c->get(\photopro\core\application\ports\api\ServiceUserInterface::class)
        );
    },
];
