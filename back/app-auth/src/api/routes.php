<?php
declare(strict_types=1);

use photopro\api\actions\SigninAction;
use photopro\api\actions\SignupAction;
use photopro\api\actions\ValidateTokenAction;

return function( \Slim\App $app):\Slim\App {

    $app->post('/signin', SigninAction::class)->setName('signin');
    $app->post('/signup', SignupAction::class)->setName('signup');
    //route pour refresh
    $app->post('/tokens/validate', ValidateTokenAction::class)->setName('tokens.validate');

    return $app;
};
