<?php
declare(strict_types=1);

use photopro\api\actions\SigninAction;
use photopro\api\actions\SignupAction;
use photopro\api\actions\ValidateTokenAction;
use photopro\api\actions\RefreshAction;
use photopro\api\actions\UpdatePhotographeAction;
use photopro\api\middlewares\AuthnMiddleware;
use photopro\api\actions\GetPhotographeProfileAction;

return function (\Slim\App $app): \Slim\App {

    $app->post('/signin', SigninAction::class)->setName('signin');
    $app->post('/signup', SignupAction::class)->setName('signup');
    $app->post('/refresh', RefreshAction::class)->setName('refresh');
    $app->post('/tokens/validate', ValidateTokenAction::class)->setName('tokens.validate');
    $app->patch('/photographes/{id}', UpdatePhotographeAction::class)
        ->add(AuthnMiddleware::class);
    $app->get('/photographes/{id}', GetPhotographeProfileAction::class)
        ->add(AuthnMiddleware::class);
    return $app;
};
