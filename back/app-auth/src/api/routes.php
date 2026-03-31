<?php
declare(strict_types=1);

use photopro\api\actions\SigninAction;
use photopro\api\actions\SignupAction;
use photopro\api\actions\ValidateTokenAction;
use photopro\api\actions\RefreshAction;

return function( \Slim\App $app):\Slim\App {

    $app->post('/signin', SigninAction::class)->setName('signin');
    $app->post('/signup', SignupAction::class)->setName('signup');
    $app->post('/refresh', RefreshAction::class)->setName('refresh');
    $app->post('/tokens/validate', ValidateTokenAction::class)->setName('tokens.validate');

    return $app;
};
