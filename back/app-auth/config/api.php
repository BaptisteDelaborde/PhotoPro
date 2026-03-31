<?php

use photopro\api\actions\SigninAction;
use photopro\api\actions\ValidateTokenAction;
use photopro\api\providers\AuthnProviderInterface;
use photopro\api\providers\JWTManager;

return [
    SigninAction::class => function($c){
        return new SigninAction(
            $c->get(AuthnProviderInterface::class)
        );
    },
    ValidateTokenAction::class => function($c){
        return new ValidateTokenAction(
            $c->get(JWTManager::class)
        );
    },
];
